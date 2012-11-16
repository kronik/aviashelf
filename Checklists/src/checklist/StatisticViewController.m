//
//  StatisticViewController.m
//  safechecklists
//
//  Created by kronik on 7/21/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "StatisticViewController.h"
#import "AppDelegate.h"

@interface StatisticViewController ()

@property (strong, nonatomic) NSArray *statLog;

@end

@implementation StatisticViewController
@synthesize statLog = _statLog;

- (void)viewDidLoad
{
    [super viewDidLoad];
    
    self.statLog = [AppDelegate getStatistic];

    [self.tableView reloadData];
    // Uncomment the following line to preserve selection between presentations.
    // self.clearsSelectionOnViewWillAppear = NO;
 
    // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
    // self.navigationItem.rightBarButtonItem = self.editButtonItem;
}

- (void)viewDidUnload
{
    self.statLog = nil;
    
    [super viewDidUnload];
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return YES;
}

#pragma mark - Table view data source

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    // Return the number of sections.
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    return self.statLog.count - 1;
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *kCellID = @"doccellID";
	
	UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellID];
	if (cell == nil)
	{
		cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleSubtitle reuseIdentifier:kCellID];
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        cell.textLabel.highlightedTextColor = [UIColor blackColor];
        cell.accessoryType = UITableViewCellAccessoryNone;
        cell.textLabel.numberOfLines = 0;
        cell.textLabel.textColor = [UIColor blackColor];
        cell.textLabel.font = [UIFont fontWithName:@"Helvetica" size:18.0]; 
        cell.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];
        //cell.selectedBackgroundView.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];//[UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-item-selected@2x.png"]];
	}
    
    NSString *statRecord = [self.statLog objectAtIndex:self.statLog.count - indexPath.row - 2];
    NSArray *tokens = [statRecord componentsSeparatedByString:@"|"];

    cell.textLabel.text = [tokens objectAtIndex:3];
    cell.detailTextLabel.text = [NSString stringWithFormat:@"Дата: %@   Выполнено: %@ / %@",
                           [tokens objectAtIndex:0], [tokens objectAtIndex:1], [tokens objectAtIndex:2]];
    
    if ([[tokens objectAtIndex:1] isEqualToString:[tokens objectAtIndex:2]])
    {
        cell.imageView.image = [UIImage imageNamed:@"checkbox_checked.png"];
    }
    else 
    {
        cell.imageView.image = [UIImage imageNamed:@"checkbox_unchecked.png"];
    }
    
    return cell;
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 80;
}

@end
