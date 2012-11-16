//
//  ViewController.h
//  checklist
//
//  Created by dima on 4/3/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "DataParser.h"

@interface MainViewController : UIViewController<DataParserDelegate>

@property (nonatomic, weak) IBOutlet UIButton *redButton;
@property (nonatomic, weak) IBOutlet UIButton *orangeButton;
@property (nonatomic, weak) IBOutlet UIButton *yellowButton;
@property (nonatomic, weak) IBOutlet UIButton *docsButton;
@property (nonatomic, weak) IBOutlet UIButton *statButton;

-(IBAction)onRedButtonPressed:(UIButton*)sender;
-(IBAction)onYellowButtonPressed:(UIButton*)sender;
-(IBAction)onOrangeButtonPressed:(UIButton*)sender;

-(IBAction)onDocsButtonPressed:(UIButton*)sender;
-(IBAction)onStatButtonPressed:(UIButton*)sender;

@end
