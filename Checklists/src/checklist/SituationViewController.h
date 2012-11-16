//
//  SituationViewController.h
//  checklist
//
//  Created by dima on 4/12/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>

@interface SituationViewController : UITableViewController

@property (nonatomic, strong) NSArray *situations;

-(IBAction)onGetBack:(id)sender;

@end
