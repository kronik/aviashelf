//
//  CheckListViewController.h
//  checklist
//
//  Created by dima on 4/12/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Situation.h"
#import "ReaderViewController.h"

@interface CheckListViewController : UITableViewController<UIAlertViewDelegate, ReaderViewControllerDelegate>

@property (nonatomic, strong) Situation *situation;

@property (weak, nonatomic) IBOutlet UIButton *titleButton;
@property (weak, nonatomic) IBOutlet UIBarButtonItem *resetButton;

-(IBAction)onResetActions:(id)sender;
-(IBAction)onGetBack:(id)sender;
-(IBAction)onShowDetails:(id)sender;

@end
