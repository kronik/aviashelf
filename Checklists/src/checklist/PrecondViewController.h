//
//  SituationViewController.h
//  checklist
//
//  Created by dima on 4/12/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "Situation.h"

@interface PrecondViewController : UITableViewController

@property (nonatomic, strong) Situation *situation;

-(IBAction)onShowActions:(id)sender;
-(IBAction)onGetBack:(id)sender;

@end
