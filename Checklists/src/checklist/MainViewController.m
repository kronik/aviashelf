//
//  ViewController.m
//  checklist
//
//  Created by dima on 4/3/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "MainViewController.h"
#import "DataParser.h"
#import "SituationViewController.h"
#import "QuartzCore/QuartzCore.h"
#import "AppDelegate.h"

@interface MainViewController ()

@property (nonatomic, strong) DataParser *parser;
@property (nonatomic, strong) NSString *selectedTitle;

-(void)loadDataFile: (NSString*)fileName;
-(void)setShadowForView: (UIView*)view;
-(CALayer *)createShadowWithFrame:(CGRect)frame;

@end

@implementation MainViewController

@synthesize parser = _parser;
@synthesize redButton = _redButton;
@synthesize orangeButton = _orangeButton;
@synthesize yellowButton = _yellowButton;
@synthesize docsButton = _docsButton;
@synthesize selectedTitle = _selectedTitle;
@synthesize statButton = _statButton;

-(CALayer *)createShadowWithFrame:(CGRect)frame
{
    CAGradientLayer *gradient = [CAGradientLayer layer];
    gradient.frame = frame;
    
    UIColor* lightColor = [[UIColor blackColor] colorWithAlphaComponent:0.0];
    UIColor* darkColor = [[UIColor blackColor] colorWithAlphaComponent:0.3];
    
    gradient.colors = [NSArray arrayWithObjects:(id)darkColor.CGColor, (id)lightColor.CGColor, nil];
    
    return gradient;
}

-(void)setShadowForView: (UIView*)view
{
    view.layer.shadowColor = [UIColor blackColor].CGColor;
    view.layer.shadowOpacity = 1.0;
    view.layer.shadowRadius = 10;
    view.layer.shadowOffset = CGSizeMake(5.0f, 5.0f);
    
    view.layer.cornerRadius = 10.0f;
    view.clipsToBounds = YES;
    view.layer.borderWidth = 3.0f;
}

- (void)loadDataFile: (NSString*)fileName
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    [self.parser parseFile:fileName];
}

-(IBAction)onRedButtonPressed:(UIButton*)sender
{
    [self loadDataFile:@"АВАРИЙНЫЕ СИТУАЦИИ РЛЭ"];
}

-(IBAction)onYellowButtonPressed:(UIButton*)sender
{
    [self loadDataFile:@"СЛОЖНЫЕ СИТУАЦИИ РПП"];
}

-(IBAction)onOrangeButtonPressed:(UIButton*)sender
{
    [self loadDataFile:@"СЛОЖНЫЕ СИТУАЦИИ РЛЭ"];
}

-(IBAction)onDocsButtonPressed:(UIButton*)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];
}

-(IBAction)onStatButtonPressed:(UIButton*)sender
{
    
}

-(void)viewDidAppear:(BOOL)animated
{
    self.title = @"Аварийные карты";
    
    [self updateUI];
}

-(void)viewDidDisappear:(BOOL)animated
{
    self.title = @"Назад";
}

- (void)updateUI
{
    NSString *dataFile = @"СЛОЖНЫЕ СИТУАЦИИ РПП";
    
    NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
    NSString *path = [documentsDirectory stringByAppendingPathComponent:[NSString stringWithFormat:@"%@.xml", dataFile]];
    
    self.yellowButton.hidden = ( [[NSFileManager defaultManager] fileExistsAtPath: path] == NO) ? YES : NO;
}

- (void)viewDidLoad
{
    _parser = [[DataParser alloc] init];
    self.parser.delegate = self;
    
    [self setShadowForView: self.redButton];
    [self setShadowForView: self.orangeButton];
    [self setShadowForView: self.yellowButton];
    [self setShadowForView: self.docsButton];
    [self setShadowForView: self.statButton];

    /*
    self.navigationController.navigationBar.titleTextAttributes = [NSDictionary dictionaryWithObjectsAndKeys:
            [UIColor blackColor], UITextAttributeTextShadowColor,
                                                                   //[UIFont fontWithName:@"System-bold" size:30.0], UITextAttributeFont,
            [UIColor whiteColor], UITextAttributeTextColor, nil];
    */
    [super viewDidLoad];
	// Do any additional setup after loading the view, typically from a nib.    
}

- (void)dataParserDidFinish:(DataParser *)dataParser situations:(NSArray*)situations;
{
    /*
    SituationViewController *situationsViewController = [[SituationViewController alloc] initWithNibName:@"SituationView" bundle:nil];
    situationsViewController.situations = situations;
    [self.navigationController pushViewController:situationsViewController animated:YES];
     */
    
    [self performSegueWithIdentifier:@"ShowSituations" sender:self];
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    if ([segue.destinationViewController respondsToSelector:@selector(setSituations:)]) 
    {
        
        NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
        NSString *path = [documentsDirectory stringByAppendingPathComponent:@"data.bin"];
        
        BOOL isSaved = [self.parser.situations writeToFile:path atomically:YES];
        NSLog(@"File saved: %@", isSaved?@"YES":@"NO");
        
        // use performSelector:withObject: to send without compiler checking
        // (which is acceptable here because we used introspection to be sure this is okay)
        [segue.destinationViewController performSelector:@selector(setSituations:) withObject:self.parser.situations];
    }
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    
    self.parser = nil;
    self.selectedTitle = nil;
    self.docsButton = nil;
    self.redButton = nil;
    self.yellowButton = nil;
    self.orangeButton = nil;
    self.statButton = nil;
    // Release any retained subviews of the main view.
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return YES;
}

@end
