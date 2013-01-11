//
//  SettingsViewController.m
//  safechecklists
//
//  Created by kronik on 7/18/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "SettingsViewController.h"
#import "UIProgressAlertView.h"
#import "AppDelegate.h"

@interface SettingsViewController ()

@property (strong, nonatomic) UIProgressAlertView *progressView;

@end

@implementation SettingsViewController

@synthesize useVoiceSwitch = _useVoiceSwitch;
@synthesize isLinkedSwitch = _isLinkedSwitch;
@synthesize progressView = _progressView;

#define USE_VOICE_SETTING @"USE_VOICE_SETTING"

+(BOOL)isVoiceOn
{
    return [SettingsViewController getUseVoice];
}

+(BOOL)getUseVoice
{
    NSString *useVoice = nil;
    
    useVoice = [[NSUserDefaults standardUserDefaults] stringForKey:USE_VOICE_SETTING];
    if (useVoice == nil || [useVoice isEqualToString:@"1"])
    {
        return YES;
    }
    return NO;
}

+(void)setUseVoice: (BOOL)useVoice
{   
    NSString *useVoiceStr = useVoice ? @"1" : @"0";
    
    [[NSUserDefaults standardUserDefaults] setValue:useVoiceStr forKey:USE_VOICE_SETTING];
    [[NSUserDefaults standardUserDefaults] synchronize];
}


-(void)backPressed: (id)sender
{
    if (self.progressView == nil)
    {
        [self.navigationController popViewControllerAnimated: YES]; // or popToRoot... if required.
    }
    else 
    {
    }
}

-(IBAction)onUseVoiceSwitchChanged:(id)sender
{
    [SettingsViewController setUseVoice: self.useVoiceSwitch.isOn];
}

-(IBAction)onIsLinkedSwitchChanged:(id)sender
{
    if (self.isLinkedSwitch.isOn)
    {
        [[DBSession sharedSession] linkFromController:self];
    }
    else 
    {
        [[DBSession sharedSession] unlinkAll];
    }
}

- (void)cleanDocumentsFolder
{
    NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
    NSArray *files = [[NSFileManager defaultManager] contentsOfDirectoryAtPath:documentsDirectory error:nil];
    
    NSError *error = nil;
    
    for (NSString *file in files)
    {
        NSString *filePath = [documentsDirectory stringByAppendingPathComponent:file];
        
        [[NSFileManager defaultManager] removeItemAtPath:filePath error:&error];
        
        if (error != nil)
        {
            NSLog(@"Error while deleting file: %@", error);
        }
        else 
        {
        }
        error = nil;
    }
}

- (void)deleteDownloadedContent: (NSString*) ext
{
    NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
    
    if (paths.count > 0)
    {
        NSError *error = nil;
        NSFileManager *fileManager = NSFileManager.defaultManager;
        
        // For each file in the directory, create full path and delete the file
        for (NSString *file in [fileManager contentsOfDirectoryAtPath:paths[0] error:&error])
        {
            NSString *filePath = [paths[0] stringByAppendingPathComponent:file];
            [[NSFileManager defaultManager] removeItemAtPath:filePath error:nil];
        }
    }    
}

- (void) alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex
{
    if (buttonIndex > 0)
    {
        self.progressView = [[UIProgressAlertView alloc] initWithTitle:@"Сброс настроек..." delegate:self cancelButtonTitle:nil otherButtonTitles:nil];
        [self.progressView show];
        
        dispatch_queue_t processQueue = dispatch_queue_create("resetBlock", NULL);
        dispatch_async(processQueue, ^{
            [[DBSession sharedSession] unlinkAll];
            [SettingsViewController setUseVoice:YES];
            
            [self.useVoiceSwitch setOn: [SettingsViewController getUseVoice]];
            [self.isLinkedSwitch setOn: [[DBSession sharedSession] isLinked]];
            
            [AppDelegate resetStatistic];
            
            [self cleanDocumentsFolder];
            
            [self deleteDownloadedContent: @"xml"];
            [self deleteDownloadedContent: @"pdf"];

            dispatch_async(dispatch_get_main_queue(), ^{
                
                [self.progressView dismissWithClickedButtonIndex:-1 animated:YES];
                self.progressView = nil;
                
                UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Готово!" message:nil delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
                [alert show];
            });
        });
        
        dispatch_release(processQueue);
    }
}

-(void)onResetPressed:(id)sender
{   
    UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Внимание!" message:@"Вы действительно хотите сбросить все настройки в изначальное состояние?" delegate:self cancelButtonTitle:@"Нет" otherButtonTitles:@"Да", nil];
    [alert show];
}

- (void)viewDidLoad
{
    [super viewDidLoad];
	// Do any additional setup after loading the view.
    
    [self.useVoiceSwitch setOn: [SettingsViewController getUseVoice]];
    [self.isLinkedSwitch setOn: [[DBSession sharedSession] isLinked]];
    
    UIBarButtonItem *btn = [[UIBarButtonItem alloc] initWithTitle:@"Назад" style:UIBarButtonItemStyleBordered target:self action:@selector(backPressed:)];
    btn.tintColor = [UIColor blueColor];
    self.navigationItem.leftBarButtonItem = btn;
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    // Release any retained subviews of the main view.
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return YES;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{ 
    if (indexPath.row == 2)
    {
        //RESET
        [self onResetPressed:self];
    }
}

@end
