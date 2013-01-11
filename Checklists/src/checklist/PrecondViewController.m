//
//  SituationViewController.m
//  checklist
//
//  Created by dima on 4/12/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "PrecondViewController.h"
#import "Situation.h"
#import "Preconditions.h"
#import "CheckListViewController.h"
#import "AppDelegate.h"

@interface PrecondViewController ()

@end

@implementation PrecondViewController

#define FONT_SIZE 30

@synthesize situation = _situation;

-(void)setSituation:(Situation *)situation
{
    _situation = situation;
    [((UITableView*)self.view) reloadData];
}

- (void)prepareForSegue:(UIStoryboardSegue *)segue sender:(id)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    if ([segue.destinationViewController respondsToSelector:@selector(setSituation:)]) 
    {
        [segue.destinationViewController performSelector:@selector(setSituation:) withObject:self.situation];
    }
}

- (UIView *) tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section 
{
    Preconditions *precond = [self.situation.preconditions objectAtIndex:section];

    UIView* customView = [[UIView alloc] initWithFrame:CGRectMake(10.0, 0.0, tableView.bounds.size.width, 18)];
    UILabel * headerLabel = [[UILabel alloc] initWithFrame:CGRectZero];
	headerLabel.backgroundColor = [UIColor clearColor];
	headerLabel.opaque = NO;
    headerLabel.numberOfLines = 0;
    headerLabel.lineBreakMode = UILineBreakModeWordWrap;
	headerLabel.textColor = [UIColor whiteColor];
	headerLabel.highlightedTextColor = [UIColor whiteColor];
	headerLabel.font = [UIFont boldSystemFontOfSize:20];
	headerLabel.frame = CGRectMake(30.0, -10.0, tableView.bounds.size.width, 44.0);
    
	// If you want to align the header text as centered
	// headerLabel.frame = CGRectMake(150.0, 0.0, 300.0, 44.0);
    
	headerLabel.text = [NSString stringWithFormat:@"%@:", precond.header];
    
	[customView addSubview:headerLabel];
    
	return customView;
}

-(IBAction)onShowActions:(id)sender
{
    [self performSegueWithIdentifier:@"ShowActions" sender:self];
}

-(IBAction)onGetBack:(id)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    [self.navigationController popViewControllerAnimated:YES];
}
/*
-(IBAction)onShowActionsPressed:(id)sender
{
    [self performSegueWithIdentifier:@"ShowActions" sender:self];
}
*/

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return self.situation.preconditions.count;
}

- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section
{
    Preconditions *precond = [self.situation.preconditions objectAtIndex:section];
    return precond.header;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    Preconditions *precond = [self.situation.preconditions objectAtIndex:section];
    return [precond count];
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
    static NSString *kCellID = @"precondcellID";
    NSString *title = nil;
    Preconditions *precond = nil;
	
	UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellID];
	if (cell == nil)
	{
		cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellID];
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        cell.textLabel.highlightedTextColor = [UIColor blackColor];
        cell.accessoryType = UITableViewCellAccessoryNone;
        cell.textLabel.numberOfLines = 0;
        cell.textLabel.adjustsFontSizeToFitWidth = NO;
        cell.textLabel.font = [UIFont boldSystemFontOfSize:FONT_SIZE];
        cell.textLabel.minimumFontSize = FONT_SIZE;
        cell.textLabel.textColor = [UIColor darkGrayColor];
        cell.backgroundColor = [UIColor whiteColor]; //[UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];
        //[cell setUserInteractionEnabled:NO];
	}
    
    precond = [self.situation.preconditions objectAtIndex:indexPath.section];
    title = [precond preconditionAtIndex:indexPath.row];
    
    cell.textLabel.text = title;
    [cell.textLabel sizeToFit];
    cell.imageView.image = [UIImage imageNamed:@"exclamation.png"];
    cell.imageView.contentMode = UIViewContentModeScaleAspectFit;
	return cell;
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    UIInterfaceOrientation orientation = [[UIDevice currentDevice] orientation];
    
    CGRect cgRect =[[UIScreen mainScreen] bounds];
    CGSize cgSize = cgRect.size;
    
    float CELL_CONTENT_WIDTH = cgSize.width;
    float CELL_CONTENT_MARGIN = 60.0f;
    
    if(orientation == UIInterfaceOrientationPortrait || orientation == UIInterfaceOrientationPortraitUpsideDown) 
    {
        CELL_CONTENT_WIDTH = cgSize.width;
    }
    else
    if (orientation==UIInterfaceOrientationLandscapeRight || orientation==UIInterfaceOrientationLandscapeLeft ) 
    {
        CELL_CONTENT_WIDTH = cgSize.height;
    }
        
    NSString *title = [[self.situation.preconditions objectAtIndex:indexPath.section] preconditionAtIndex:indexPath.row];
    
    CGSize constraint = CGSizeMake(CELL_CONTENT_WIDTH - (CELL_CONTENT_MARGIN * 2), 20000.0f);
    
    CGSize size = [title sizeWithFont:[UIFont systemFontOfSize:FONT_SIZE] constrainedToSize:constraint lineBreakMode:UILineBreakModeWordWrap];
    
    CGFloat height = MAX(size.height, 44.0f);
    
    return height + (CELL_CONTENT_MARGIN * 2);
    
    /*
    UIInterfaceOrientation orientation = [[UIDevice currentDevice] orientation];
    
    if(orientation==UIInterfaceOrientationPortrait ||orientation==UIInterfaceOrientationPortraitUpsideDown) 
    {
        return 360.0;
    }
    
    if (orientation==UIInterfaceOrientationLandscapeRight ||orientation==UIInterfaceOrientationLandscapeLeft ) 
    {
        return 220.0;
    }
    
    return 360.0;
     */
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{   
    [[[AppDelegate appDelegate] clickPlayer] play];

    //UITableViewCell *cell = [tableView cellForRowAtIndexPath:indexPath];        
    //cell.accessoryType = UITableViewCellAccessoryCheckmark;
    
    int nextRow = indexPath.row;
    int nextSection = indexPath.section;
    
    if (nextRow + 1 == [[self.situation.preconditions objectAtIndex:indexPath.section] count])
    {
        if (nextSection + 1 == self.situation.preconditions.count)
        {
        }
        else 
        {
            nextSection += 1;
            nextRow = 0;
        }
    }
    else 
    {
        nextRow += 1;
    }
    
    NSIndexPath *newIndexPath = [NSIndexPath indexPathForRow:nextRow inSection:nextSection];
    
    [self.tableView scrollToRowAtIndexPath:newIndexPath atScrollPosition:UITableViewScrollPositionMiddle animated:YES];
}

- (void)viewDidLoad
{
    UIImageView *imageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"LionDefault.png"]];
    
    self.tableView.backgroundView = imageView;
    
    [super viewDidLoad];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    
    self.situation = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return YES;
}

@end
