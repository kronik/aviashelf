//
//  AppDelegate.h
//  checklist
//
//  Created by dima on 4/3/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <AVFoundation/AVFoundation.h>
#import <DropboxSDK/DropboxSDK.h>

@interface AppDelegate : UIResponder <UIApplicationDelegate, DBSessionDelegate, DBNetworkRequestDelegate>

@property (strong, nonatomic) UIWindow *window;
@property (nonatomic, strong) AVAudioPlayer *clickPlayer;
@property (nonatomic, strong) AVAudioPlayer *tapPlayer;
@property (nonatomic, strong) AVAudioPlayer *completePlayer;

@property (strong, nonatomic) NSString *relinkUserId;

+ (AppDelegate *)appDelegate;

+ (void)saveStatistic: (NSString*) title score: (int) score totalScore: (int)totalScore;
+ (NSArray*)getStatistic;
+ (void)resetStatistic;

@end
