//
//  SettingsViewController.h
//  safechecklists
//
//  Created by kronik on 7/18/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <DropboxSDK/DropboxSDK.h>
#import "Reachability.h"
#include <sys/xattr.h>

@interface SettingsViewController : UITableViewController <UITableViewDelegate, UIAlertViewDelegate>

@property (weak, nonatomic) IBOutlet UISwitch *useVoiceSwitch;
@property (weak, nonatomic) IBOutlet UISwitch *isLinkedSwitch;

-(IBAction)onUseVoiceSwitchChanged:(id)sender;
-(IBAction)onIsLinkedSwitchChanged:(id)sender;

-(void)onResetPressed:(id)sender;

+(BOOL)isVoiceOn;

@end
