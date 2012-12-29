//
//  AppDelegate.m
//  checklist
//
//  Created by dima on 4/3/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "AppDelegate.h"
#import "TestFlight.h"
#include <sys/xattr.h>

@implementation AppDelegate

@synthesize clickPlayer = _clickPlayer;
@synthesize tapPlayer = _tapPlayer;
@synthesize completePlayer = _completePlayer;
@synthesize relinkUserId = _relinkUserId;

@synthesize window = _window;

+ (AppDelegate *)appDelegate 
{
	return (AppDelegate *)[[UIApplication sharedApplication] delegate];
}

+ (void)playSound: (NSString*) soundFile
{
    if (soundFile == nil || [soundFile isEqualToString:@""])
    {
        return;
    }

    NSString *soundPath = [[NSBundle mainBundle] pathForResource:soundFile ofType:@"mp3"];
    NSError *error;
    
    [AppDelegate stopPlaySound];

    [AppDelegate appDelegate].generalPlayer = [[AVAudioPlayer alloc] initWithContentsOfURL:[NSURL fileURLWithPath:soundPath] error:&error];

    [AppDelegate appDelegate].generalPlayer.numberOfLoops = 0;
    [AppDelegate appDelegate].generalPlayer.volume = 1.0f;
    
    [[AppDelegate appDelegate].generalPlayer play];
}

+ (void)stopPlaySound
{
    if ([AppDelegate appDelegate].generalPlayer != nil)
    {
        [[AppDelegate appDelegate].generalPlayer stop];
    }
}

-(AVAudioPlayer*)clickPlayer
{
    if (_clickPlayer != nil)
    {
        [_clickPlayer stop];
        _clickPlayer = nil;
    }
    
    NSString *clickPath = [[NSBundle mainBundle] pathForResource:@"select" ofType:@"wav"];
    NSError *error;

    _clickPlayer = [[AVAudioPlayer alloc] initWithContentsOfURL:[NSURL fileURLWithPath:clickPath] error:&error];
    _clickPlayer.numberOfLoops = 0;
    _clickPlayer.volume = 0.5f;
    [_clickPlayer prepareToPlay];
    
    return _clickPlayer;
}

-(AVAudioPlayer*)tapPlayer
{
    if (_tapPlayer != nil)
    {
        [_tapPlayer stop];
        _tapPlayer = nil;
    }
    NSString *tapPath = [[NSBundle mainBundle] pathForResource:@"tap" ofType:@"aif"];
    NSError *error;
    
    _tapPlayer = [[AVAudioPlayer alloc] initWithContentsOfURL:[NSURL fileURLWithPath:tapPath] error:&error];
    _tapPlayer.numberOfLoops = 0;
    _tapPlayer.volume = 0.3f;
    
    [_tapPlayer prepareToPlay];

    return _tapPlayer;
}

-(AVAudioPlayer*)completePlayer
{
    if (_completePlayer != nil)
    {
        [_completePlayer stop];
        _completePlayer = nil;
    }
    NSString *tapPath = [[NSBundle mainBundle] pathForResource:@"complete" ofType:@"wav"];
    NSError *error;
    
    _completePlayer = [[AVAudioPlayer alloc] initWithContentsOfURL:[NSURL fileURLWithPath:tapPath] error:&error];
    _completePlayer.numberOfLoops = 0;
    _completePlayer.volume = 0.5f;
    
    [_completePlayer prepareToPlay];
    
    return _completePlayer;
}

#define STATISTIC_RECORD_FORMAT @"%@|%d|%d|%@\n"

+ (void)saveStatistic: (NSString*) title score: (int) score totalScore: (int)totalScore
{
    dispatch_queue_t processQueue = dispatch_queue_create("saveLog", NULL);
    dispatch_async(processQueue, ^{

        NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];

        NSString *fileName = @"statistic.txt";
        
        NSString *statisticFilePath = [documentsDirectory stringByAppendingPathComponent:fileName];
        
        NSURL *fileUrl = [NSURL URLWithString:statisticFilePath];
        
        if( [[NSFileManager defaultManager] fileExistsAtPath: statisticFilePath] == NO)
        {
            [[NSFileManager defaultManager] createFileAtPath:statisticFilePath contents:nil attributes:nil];
            
            u_int8_t b = 1;
            setxattr([[fileUrl path] fileSystemRepresentation], "com.apple.MobileBackup", &b, 1, 0, 0);
        }

        NSDate *now = [[NSDate alloc] init];
        NSDateFormatter *timeFormat = [[NSDateFormatter alloc] init];
        [timeFormat setDateFormat:@"dd-MM-yyyy HH:mm:ss"];
        NSString *date = [timeFormat stringFromDate:now];
        
        NSString *newStatRecord = [NSString stringWithFormat:STATISTIC_RECORD_FORMAT, date, score, totalScore, title];

        NSFileHandle *fileHandler = [NSFileHandle fileHandleForUpdatingAtPath:statisticFilePath];
        [fileHandler seekToEndOfFile];
        [fileHandler writeData:[newStatRecord dataUsingEncoding:NSUTF8StringEncoding]];
        [fileHandler closeFile];
        
    });
    
    dispatch_release(processQueue);
}

+ (NSArray*)getStatistic
{
    //[AppDelegate createSampleStatisticDataOfSize:1 ofType:type];
    
    NSArray *statistic = [[NSArray alloc] init];
    
    NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];

    NSString *fileName = @"statistic.txt";
    
    NSString *statisticFilePath = [documentsDirectory stringByAppendingPathComponent:fileName];
    
    if( [[NSFileManager defaultManager] fileExistsAtPath: statisticFilePath] == YES)
    {
        statistic = [[NSString stringWithContentsOfFile:statisticFilePath encoding:NSUTF8StringEncoding error:nil] 
                     componentsSeparatedByString:@"\n"];
    }
    
    return statistic;
}

+ (void)resetStatistic
{
    NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
    
    NSString *fileName = @"statistic.txt";
    
    NSString *statisticFilePath = [documentsDirectory stringByAppendingPathComponent:fileName];
    
    if( [[NSFileManager defaultManager] fileExistsAtPath: statisticFilePath] == YES)
    {
        [[NSFileManager defaultManager] removeItemAtPath:statisticFilePath error:nil];
    }
}

- (void)setDisableSyncForURL: (NSURL*)url
{
    u_int8_t b = 1;
    setxattr([[url path] fileSystemRepresentation], "com.apple.MobileBackup", &b, 1, 0, 0);
}

- (void)copyToDocumentsBundleFilesWithExt: (NSString*) ext
{
    NSArray *pdfs = [[NSBundle mainBundle] pathsForResourcesOfType:ext inDirectory:nil];
    
    NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
    
    for (NSString *sourcePath in pdfs)
    {
        NSError *error = nil;
        NSArray *tokens = [sourcePath componentsSeparatedByString:@"/"];
        
        NSString *folderPath = [documentsDirectory stringByAppendingPathComponent:[tokens objectAtIndex:tokens.count-1]];
        
        if( [[NSFileManager defaultManager] fileExistsAtPath: sourcePath] )
        {
        }
        
        if( [[NSFileManager defaultManager] fileExistsAtPath: folderPath] == NO)
        {
            if([[NSFileManager defaultManager] copyItemAtPath:sourcePath toPath:folderPath error:&error])
            {
                [self setDisableSyncForURL:[NSURL URLWithString:folderPath]];
            }
            else 
            {
                NSLog(@"Error description-%@ \n", [error localizedDescription]);
                NSLog(@"Error reason-%@", [error localizedFailureReason]);
            }
        }
    }
}

- (BOOL)application:(UIApplication *)application didFinishLaunchingWithOptions:(NSDictionary *)launchOptions
{
    //[[UIApplication sharedApplication] setStatusBarStyle:UIStatusBarStyleBlackOpaque animated:NO];
    
    NSString* appKey = @"ovg3iomyafzxku0";
	NSString* appSecret = @"gwpzm0042pj6rzj";
    NSString *root = kDBRootDropbox;
    
    NSString* errorMsg = nil;
	if ([appKey rangeOfCharacterFromSet:[[NSCharacterSet alphanumericCharacterSet] invertedSet]].location != NSNotFound) 
    {
		errorMsg = @"Make sure you set the app key correctly in DBRouletteAppDelegate.m";
	} 
    else if ([appSecret rangeOfCharacterFromSet:[[NSCharacterSet alphanumericCharacterSet] invertedSet]].location != NSNotFound)
    {
		errorMsg = @"Make sure you set the app secret correctly in DBRouletteAppDelegate.m";
	} 
    else if ([root length] == 0)
    {
		errorMsg = @"Set your root to use either App Folder of full Dropbox";
	} 
    else 
    {
		NSString *plistPath = [[NSBundle mainBundle] pathForResource:@"Info" ofType:@"plist"];
		NSData *plistData = [NSData dataWithContentsOfFile:plistPath];
		NSDictionary *loadedPlist = 
        [NSPropertyListSerialization 
         propertyListFromData:plistData mutabilityOption:0 format:NULL errorDescription:NULL];
		NSString *scheme = [[[[loadedPlist objectForKey:@"CFBundleURLTypes"] objectAtIndex:0] objectForKey:@"CFBundleURLSchemes"] objectAtIndex:0];
        
		if ([scheme isEqual:@"db-APP_KEY"])
        {
			errorMsg = @"Set your URL scheme correctly in DBRoulette-Info.plist";
		}
	}
	
	DBSession* session = [[DBSession alloc] initWithAppKey:appKey appSecret:appSecret root:root];
	session.delegate = self; // DBSessionDelegate methods allow you to handle re-authenticating
	[DBSession setSharedSession:session];
	
	[DBRequest setNetworkRequestDelegate:self];
    
	if (errorMsg != nil) 
    {
		[[[UIAlertView alloc] initWithTitle:@"Error Configuring Session" message:errorMsg 
                                   delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil] show];
	}
    
    UIImage *navBarImage = [UIImage imageNamed:@"ipad-menubar-right@2x.png"];
    
    [[UINavigationBar appearance] setBackgroundImage:navBarImage forBarMetrics:UIBarMetricsDefault];
    
    [[UINavigationBar appearance] setTitleTextAttributes:
     [NSDictionary dictionaryWithObjectsAndKeys:
      [UIColor colorWithRed:255.0/255.0 green:255.0/255.0 blue:255.0/255.0 alpha:1.0], 
      UITextAttributeTextColor, 
      [UIColor colorWithRed:0.0 green:0.0 blue:0.0 alpha:0.8], 
      UITextAttributeTextShadowColor, 
      [NSValue valueWithUIOffset:UIOffsetMake(0, -1)], 
      UITextAttributeTextShadowOffset, 
      nil]];
    
    UIImage *backButton = [[UIImage imageNamed:@"ipad-back-red.png"] resizableImageWithCapInsets:UIEdgeInsetsMake(0, 14, 0, 4)];
    
    [[UIBarButtonItem appearance] setBackButtonBackgroundImage:backButton forState:UIControlStateNormal barMetrics:UIBarMetricsDefault];
    
    [TestFlight takeOff:@"aeca7ecb7604c3cf87510866a8256085_ODQ5OTAyMDEyLTA0LTI2IDIzOjU4OjM4Ljg2NTQ1Mg"];
    //[TestFlight setDeviceIdentifier:[[UIDevice currentDevice] uniqueIdentifier]];
    
    dispatch_queue_t processQueue = dispatch_queue_create("init", NULL);
    dispatch_async(processQueue, ^{
        AVAudioSession *session = [AVAudioSession sharedInstance];
        NSError *error;
        
        [session setCategory: AVAudioSessionCategoryPlayback error: &error];
        if (error != nil)
        {
            NSLog(@"Failed to set category on AVAudioSession");
        }
        
        BOOL active = [session setActive: YES error: nil];
        if (!active)
        {
            NSLog(@"Failed to set category on AVAudioSession");
        }

        [self copyToDocumentsBundleFilesWithExt: @"xml"];

        [self copyToDocumentsBundleFilesWithExt: @"pdf"];
        
        dispatch_async(dispatch_get_main_queue(), ^{
        });
        
    });
    
    dispatch_release(processQueue);
    
    NSURL *launchURL = [launchOptions objectForKey:UIApplicationLaunchOptionsURLKey];
	NSInteger majorVersion = 
    [[[[[UIDevice currentDevice] systemVersion] componentsSeparatedByString:@"."] objectAtIndex:0] integerValue];
	
    if (launchURL && majorVersion < 4) 
    {
		// Pre-iOS 4.0 won't call application:handleOpenURL; this code is only needed if you support
		// iOS versions 3.2 or below
		[self application:application handleOpenURL:launchURL];
		return NO;
	}
    
    return YES;
}

- (BOOL)application:(UIApplication *)application handleOpenURL:(NSURL *)url
{
	if ([[DBSession sharedSession] handleOpenURL:url])
    {
		if ([[DBSession sharedSession] isLinked]) 
        {
			//[navigationController pushViewController:rootViewController.photoViewController animated:YES];
		}
		return YES;
	}
	
	return NO;
}

#pragma mark -
#pragma mark DBSessionDelegate methods

- (void)sessionDidReceiveAuthorizationFailure:(DBSession*)session userId:(NSString *)userId 
{
	self.relinkUserId = userId;
    
	[[[UIAlertView alloc] 
      initWithTitle:@"Dropbox Session Ended" message:@"Do you want to relink?" delegate:self 
      cancelButtonTitle:@"Cancel" otherButtonTitles:@"Relink", nil]
	 show];
}


#pragma mark -
#pragma mark UIAlertViewDelegate methods

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)index 
{
	if (index != alertView.cancelButtonIndex) 
    {
		[[DBSession sharedSession] linkUserId:self.relinkUserId fromController:self.window.rootViewController];
	}
	self.relinkUserId = nil;
}


#pragma mark -
#pragma mark DBNetworkRequestDelegate methods

static int outstandingRequests;

- (void)networkRequestStarted {
	outstandingRequests++;
	if (outstandingRequests == 1) {
		[[UIApplication sharedApplication] setNetworkActivityIndicatorVisible:YES];
	}
}

- (void)networkRequestStopped {
	outstandingRequests--;
	if (outstandingRequests == 0) {
		[[UIApplication sharedApplication] setNetworkActivityIndicatorVisible:NO];
	}
}

							
- (void)applicationWillResignActive:(UIApplication *)application
{
    // Sent when the application is about to move from active to inactive state. This can occur for certain types of temporary interruptions (such as an incoming phone call or SMS message) or when the user quits the application and it begins the transition to the background state.
    // Use this method to pause ongoing tasks, disable timers, and throttle down OpenGL ES frame rates. Games should use this method to pause the game.
}

- (void)applicationDidEnterBackground:(UIApplication *)application
{
    [[UIApplication sharedApplication] setIdleTimerDisabled:NO];
    // Use this method to release shared resources, save user data, invalidate timers, and store enough application state information to restore your application to its current state in case it is terminated later. 
    // If your application supports background execution, this method is called instead of applicationWillTerminate: when the user quits.
}

- (void)applicationWillEnterForeground:(UIApplication *)application
{
    // Called as part of the transition from the background to the inactive state; here you can undo many of the changes made on entering the background.
}

- (void)applicationDidBecomeActive:(UIApplication *)application
{
    [[UIApplication sharedApplication] setIdleTimerDisabled:YES];
    // Restart any tasks that were paused (or not yet started) while the application was inactive. If the application was previously in the background, optionally refresh the user interface.
}

- (void)applicationWillTerminate:(UIApplication *)application
{
    [[UIApplication sharedApplication] setIdleTimerDisabled:NO];
    // Called when the application is about to terminate. Save data if appropriate. See also applicationDidEnterBackground:.
}

@end
