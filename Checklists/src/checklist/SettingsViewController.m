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

- (void)setDisableSyncForURL: (NSURL*)url
{
    u_int8_t b = 1;
    setxattr([[url path] fileSystemRepresentation], "com.apple.MobileBackup", &b, 1, 0, 0);
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

- (void)copyToDocumentsBundleFilesWithExt: (NSString*) ext
{
    NSArray *pdfs = [[NSBundle mainBundle] pathsForResourcesOfType:ext inDirectory:nil];
    
    NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
    
    for (NSString *sourcePath in pdfs)
    {
        NSError *error = nil;
        NSArray *tokens = [sourcePath componentsSeparatedByString:@"/"];
        NSString *docName = [tokens objectAtIndex:tokens.count-1];
        
        NSString *folderPath = [documentsDirectory stringByAppendingPathComponent:docName];
        
        if( [[NSFileManager defaultManager] fileExistsAtPath: folderPath] == YES)
        {
            [[NSFileManager defaultManager] removeItemAtPath:folderPath error:nil];
        }
        
        if([[NSFileManager defaultManager] copyItemAtPath:sourcePath toPath:folderPath error:&error])
        {
            [self setDisableSyncForURL:[NSURL URLWithString:folderPath]];
            
            const char *tmp = [docName fileSystemRepresentation];
            
            docName = [NSString stringWithCString:tmp encoding:NSUTF8StringEncoding];

            [[NSUserDefaults standardUserDefaults] setValue:@"" forKey:docName];
            [[NSUserDefaults standardUserDefaults] synchronize];            
        }
        else 
        {
            NSLog(@"Error description-%@ \n", [error localizedDescription]);
            NSLog(@"Error reason-%@", [error localizedFailureReason]);
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
            
            [self copyToDocumentsBundleFilesWithExt: @"xml"];
            [self copyToDocumentsBundleFilesWithExt: @"pdf"];

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
