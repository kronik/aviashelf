//
//  SituationViewController.m
//  checklist
//
//  Created by dima on 4/12/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "SituationViewController.h"
#import "Situation.h"
#import "CheckListViewController.h"
#import "AppDelegate.h"

@interface SituationViewController ()

@property (nonatomic, weak) Situation *selectedSituation;

@end

@implementation SituationViewController

@synthesize situations = _situations;
@synthesize selectedSituation = _selectedSituation;

-(void)setSituations:(NSArray *)situations
{
    _situations = situations;
    [((UITableView*)self.view) reloadData];
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    if ([segue.destinationViewController respondsToSelector:@selector(setSituation:)]) 
    {
        [segue.destinationViewController performSelector:@selector(setSituation:) withObject:self.selectedSituation];
    }
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section
{
    return @"";
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return self.situations.count;
}

- (void)tableView:(UITableView *)tableView willDisplayCell:(UITableViewCell *)cell forRowAtIndexPath:(NSIndexPath *)indexPath
{
}

- (BOOL)tableView:(UITableView *)tableView canPerformAction:(SEL)action forRowAtIndexPath:(NSIndexPath *)indexPath withSender:(id)sender
{
    return NO;//(action == @selector(copy:));
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *kCellID = @"cellID";
    NSString *title = nil;
    Situation *situation = nil;
	
	UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellID];
	if (cell == nil)
	{
		cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellID];
        cell.selectionStyle = UITableViewCellSelectionStyleBlue;
        cell.textLabel.highlightedTextColor = [UIColor blackColor];
        cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
        cell.textLabel.numberOfLines = 0;
        cell.textLabel.textColor = [UIColor darkGrayColor];
        cell.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];
        cell.selectedBackgroundView.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-item-selected@2x.png"]];
	}
    
    switch (indexPath.section)
    {
        case 0:
            situation = [self.situations objectAtIndex:indexPath.row];
            title = [NSString stringWithFormat:@"%d. %@", indexPath.row + 1, situation.title];
            break;
        default:
            NSLog(@"ERROR: Strange section index: %d!!!!!", indexPath.section);
            title = @"";
            break;
    }
    
    cell.textLabel.text = title;
    cell.imageView.image = [UIImage imageNamed:@"red_question.png"];
	return cell;
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 70.0;
}

-(IBAction)onGetBack:(id)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    [self.navigationController popViewControllerAnimated:YES];
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    self.selectedSituation = [self.situations objectAtIndex:indexPath.row];
    
    if (self.selectedSituation.preconditions.count == 0)
    {
        [self performSegueWithIdentifier:@"ShowActions" sender:self];
    }
    else 
    {
        [self performSegueWithIdentifier:@"ShowPreconditions" sender:self];
    }
    /*
    
    CheckListViewController *checkListViewController = [[CheckListViewController alloc] initWithNibName:@"CheckListView" bundle:nil];
    checkListViewController.situation = situation;
    [self.navigationController pushViewController:checkListViewController animated:YES];
    */
    //[((UITableView*)self.view) scrollToRowAtIndexPath:indexPath atScrollPosition:UITableViewScrollPositionMiddle animated:YES];
}

- (void)viewDidLoad
{
    UIImageView *imageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"LionDefault.png"]];
    self.tableView.backgroundView = imageView;
        
    [super viewDidLoad];
}

-(void)viewWillAppear:(BOOL)animated
{
    self.navigationItem.backBarButtonItem.tintColor = [UIColor redColor];
    
    [self.tableView reloadData];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    
    self.situations = nil;
    self.selectedSituation = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return YES;
}

@end
