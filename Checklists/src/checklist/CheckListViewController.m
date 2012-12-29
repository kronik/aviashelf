//
//  CheckListViewController.m
//  checklist
//
//  Created by dima on 4/12/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//
#import "CheckListViewController.h"
#import "Actions.h"
#import "Preconditions.h"
#import "BlockAlertView.h"
#import "AppDelegate.h"
#import "SettingsViewController.h"

#define WAIT_SEC_BEFORE_QUIT 5
#define FONT_SIZE 30

#define UIColorFromRGB(rgbValue) [UIColor colorWithRed:(((float)((rgbValue & 0xFF0000) >> 16))/255.0) green:(((float)((rgbValue & 0xFF00) >> 8))/255.0) blue:(((float)(rgbValue & 0xFF))/255.0) alpha:1.0]

@interface CheckListViewController ()

@property (nonatomic, strong) NSTimer *timer;
@property (nonatomic) int timerCounter;
@property (nonatomic, strong) UIAlertView *alert;
@property (nonatomic, strong) NSString *docFileName;
@property (nonatomic) int currentRow;
@property (nonatomic) int totalScore;

- (void)onGoToMainScreen: (NSTimer *)timer;
- (void)resetActions:(id)sender;

@end

@implementation CheckListViewController

@synthesize situation = _situation;
@synthesize timer = _timer;
@synthesize timerCounter = _timerCounter;
@synthesize alert = _alert;
@synthesize titleButton = _titleButton;
@synthesize docFileName = _docFileName;
@synthesize currentRow = _currentRow;
@synthesize totalScore = _totalScore;
@synthesize resetButton = _resetButton;

-(void)setSituation:(Situation *)situation
{
    _situation = situation;
    
    NSLog(@"Title: %@", situation.title);
    
    [((UITableView*)self.view) reloadData];
    
    for (NSArray *actions in self.situation.actions)
    {
        self.totalScore += actions.count;
    }
    
    self.currentRow = 0;
    
    [self resetActions: nil];
    
    [self performSelector:@selector(playActionSound:) withObject:[self.situation.actions[0] actionSoundAtIndex: 0] afterDelay: 0.2];
}

-(void)onGoToMainScreen: (NSTimer *)timer
{
    if (self.timerCounter < WAIT_SEC_BEFORE_QUIT)
    {
        self.timerCounter++;

        self.titleButton.titleLabel.text = [NSString stringWithFormat:@"%d", WAIT_SEC_BEFORE_QUIT - self.timerCounter + 1];

        return;
    }
    
    [[[AppDelegate appDelegate] tapPlayer] play];

    [self.timer invalidate];
    self.timer = nil;
    
    [self resetActions:self];
    
    [self.navigationController popToRootViewControllerAnimated:YES];
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return self.situation.actions.count;
}

- (NSString *)tableView:(UITableView *)tableView titleForHeaderInSection:(NSInteger)section
{
    Actions *actions = [self.situation.actions objectAtIndex:section];
    return [NSString stringWithFormat:@"%@:",  actions.header];
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    Actions *actions = [self.situation.actions objectAtIndex:section];
    return [actions count];
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
    static NSString *kCellID = @"subcellID";
    NSString *title = nil;
    Actions *actions = nil;
	
	UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellID];
	if (cell == nil)
	{
		cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleDefault reuseIdentifier:kCellID];
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        //cell.textLabel.highlightedTextColor = [UIColor blackColor];
        cell.accessoryType = UITableViewCellAccessoryNone;
        cell.textLabel.numberOfLines = 0;
        cell.textLabel.lineBreakMode = UILineBreakModeWordWrap;
        cell.textLabel.font = [UIFont boldSystemFontOfSize:FONT_SIZE];
        //cell.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];
        //cell.selectedBackgroundView.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];//[UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-item-selected@2x.png"]];
	}
    
    actions = [self.situation.actions objectAtIndex:indexPath.section];
    
    title = [NSString stringWithFormat:@"%d. %@", indexPath.row + 1, [actions actionAtIndex:indexPath.row]];
    
    cell.textLabel.text = title;
    //cell.textLabel.adjustsFontSizeToFitWidth = YES;
    //cell.textLabel.textAlignment = UITextAlignmentLeft;
    
    BOOL isDone = [actions isActionAtIdxDone:indexPath.row];
    
    cell.textLabel.textColor = isDone ? [UIColor darkGrayColor] : [UIColor blackColor];
    cell.imageView.image = isDone ? [UIImage imageNamed:@"checkbox_checked.png"] : [UIImage imageNamed:@"checkbox_unchecked.png"];
    cell.backgroundColor = isDone ? UIColorFromRGB(0xB4CC83) : [UIColor whiteColor];
    cell.selectedBackgroundView.backgroundColor = isDone ? UIColorFromRGB(0xB4CC83) : [UIColor whiteColor];
    cell.imageView.contentMode = UIViewContentModeScaleAspectFit;
    cell.accessoryType = isDone ? UITableViewCellAccessoryCheckmark : UITableViewCellAccessoryNone;
    
    [cell.textLabel sizeToFit];


    //cell.imageView.contentMode = UIViewContentModeRedraw;
    
	return cell;
}

- (IBAction)showAlert:(id)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    /*
    BlockAlertView *alert = [BlockAlertView alertWithTitle:@"Alert Title" message:@"This is an alert message"];
    
    [alert setDestructiveButtonWithTitle:@"Cancel" block:nil];
    [alert addButtonWithTitle:@"OK" block:^{
        [self resetActions: self];
    }];
    [alert show];
     */
    
    self.alert = [[UIAlertView alloc]
                          initWithTitle: @"Внимание"
                          message: @"Вы действительно хотите начать карту заново?"
                          delegate: self
                          cancelButtonTitle:@"Нет"
                          otherButtonTitles:@"Да",nil];
    [self.alert show];
}

- (void)alertView:(UIAlertView *)alertView clickedButtonAtIndex:(NSInteger)buttonIndex 
{
	if (buttonIndex == 0) 
    {
	}
	else
    {
        if (self.currentRow != 0)
        {
            [AppDelegate saveStatistic:self.situation.title score:self.currentRow totalScore:self.totalScore];
        }

        [self resetActions: self];
        
        [self performSelector:@selector(playActionSound:) withObject:[self.situation.actions[0] actionSoundAtIndex: 0] afterDelay: 0.2];
	}
    
    self.alert = nil;
}

-(IBAction)onResetActions:(id)sender
{
    [self showAlert:self];
}

-(void)resetActions:(id)sender
{
    self.currentRow = 0;
    
    for (int j=0; j<self.situation.actions.count; j++)
    {
        Actions *action = [self.situation.actions objectAtIndex:j];
        for (int i=0; i<[action count]; i++)
        {
            [action setActionAtIdxUndone:i];
            
            NSIndexPath *indexPath = [NSIndexPath indexPathForRow:i inSection:j];    
            UITableViewCell *cell = [self.tableView cellForRowAtIndexPath:indexPath];

            cell.textLabel.textColor = [UIColor darkGrayColor];
            cell.backgroundColor = [UIColor whiteColor];
            cell.imageView.image = [UIImage imageNamed:@"checkbox_unchecked.png"];
        }
    }
    
    NSIndexPath *newIndexPath = [NSIndexPath indexPathForRow:0 inSection:0];    
    [self.tableView scrollToRowAtIndexPath:newIndexPath atScrollPosition:UITableViewScrollPositionMiddle animated:YES];
    
    [self.tableView reloadData];
    
    self.titleButton.titleLabel.text = @"Контрольная карта";
    [self.titleButton setNeedsLayout];
    
    [self.timer invalidate];
    self.timer = nil;
}

/*
- (UIView *) tableView:(UITableView *)tableView viewForHeaderInSection:(NSInteger)section 
{
    Actions *actions = [self.situation.actions objectAtIndex:section];
    
    UIView* customView = [[UIView alloc] initWithFrame:CGRectMake(10.0, 0.0, tableView.bounds.size.width, 18)];
    UILabel * headerLabel = [[UILabel alloc] initWithFrame:CGRectZero];
	headerLabel.backgroundColor = [UIColor clearColor];
	headerLabel.opaque = NO;
	headerLabel.textColor = [UIColor whiteColor];
	headerLabel.highlightedTextColor = [UIColor whiteColor];
	headerLabel.font = [UIFont boldSystemFontOfSize:20];
	headerLabel.frame = CGRectMake(30.0, -5.0, tableView.bounds.size.width, 44.0);
    
	// If you want to align the header text as centered
	// headerLabel.frame = CGRectMake(150.0, 0.0, 300.0, 44.0);
    
	headerLabel.text = actions.header;
    
	[customView addSubview:headerLabel];
    
	return customView;
}
*/

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    //return 140.0;
    
    Actions *actions = [self.situation.actions objectAtIndex:indexPath.section];
    NSString *title = [NSString stringWithFormat:@"%d. %@", indexPath.row + 1, [actions actionAtIndex:indexPath.row]];
    
    UIInterfaceOrientation orientation = [[UIDevice currentDevice] orientation];
    
    CGRect cgRect =[[UIScreen mainScreen] bounds];
    CGSize cgSize = cgRect.size;
    
    float CELL_CONTENT_WIDTH = cgSize.width;
    float CELL_CONTENT_MARGIN = 40.0f;
    
    if(orientation == UIInterfaceOrientationPortrait || orientation == UIInterfaceOrientationPortraitUpsideDown) 
    {
        CELL_CONTENT_WIDTH = cgSize.width;
    }
    else if (orientation==UIInterfaceOrientationLandscapeRight || orientation==UIInterfaceOrientationLandscapeLeft ) 
    {
        CELL_CONTENT_WIDTH = cgSize.height;
    }
    
    CGSize constraint = CGSizeMake(CELL_CONTENT_WIDTH - (CELL_CONTENT_MARGIN * 2), 20000.0f);
    
    CGSize size = [title sizeWithFont:[UIFont systemFontOfSize:FONT_SIZE] constrainedToSize:constraint lineBreakMode:UILineBreakModeWordWrap];
    
    CGFloat height = MAX(size.height, 44.0f);
    
    return height + (CELL_CONTENT_MARGIN * 2);
}

-(void)playSound
{
    [[[AppDelegate appDelegate] clickPlayer] play];
}

-(void)playActionSound: (NSString*)actionSound
{
    if ([SettingsViewController isVoiceOn])
    {
        [AppDelegate playSound: actionSound];
    }
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{    
    UITableViewCell *cell = [tableView cellForRowAtIndexPath:indexPath];
    BOOL isLastCellSelected = NO;
    
    int prevRow = indexPath.row;
    int prevSection = indexPath.section;

    if (prevSection != 0 || prevRow != 0)
    {
        if (prevRow != 0)
        {
            prevRow --;
        }
        else 
        {
            prevSection --;
            prevRow = [[self.situation.actions objectAtIndex:prevSection] count] - 1;
        }
        
        if ([[self.situation.actions objectAtIndex:prevSection] isActionAtIdxDone:prevRow] == NO)
        {
            return;
        }
    }

    //TODO: May be set another background
    
    int nextRow = indexPath.row;
    int nextSection = indexPath.section;
    
    if (nextRow + 1 == [[self.situation.actions objectAtIndex:indexPath.section] count])
    {
        if (nextSection + 1 == self.situation.actions.count)
        {
            //TODO: DONE (enable DONE button)
            //TODO: If there is no more actions to do:
            self.timerCounter = 0;
            self.timer = [NSTimer scheduledTimerWithTimeInterval:1.0
                                                          target:self
                                                        selector:@selector(onGoToMainScreen:)
                                                        userInfo:nil
                                                         repeats:YES];
            isLastCellSelected = YES;

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

    Actions *actions = [self.situation.actions objectAtIndex:indexPath.section];
    BOOL isDone = [actions isActionAtIdxDone:indexPath.row];

    if (isDone == NO)
    {
        self.currentRow++;
        [actions setActionAtIdxDone:indexPath.row];
    }
    
    cell.imageView.image = [UIImage imageNamed:@"checkbox_checked.png"];
    cell.accessoryType = UITableViewCellAccessoryCheckmark;
    cell.textLabel.textColor = [UIColor darkGrayColor];
    cell.backgroundColor = UIColorFromRGB(0xB4CC83);
    

    if (isLastCellSelected)
    {
        [AppDelegate saveStatistic:self.situation.title score:self.currentRow totalScore:self.totalScore];
        
        [[[AppDelegate appDelegate] completePlayer] play];
        
        self.currentRow = 0;
    }
    else 
    {
        [[[AppDelegate appDelegate] clickPlayer] play];
        
        if (newIndexPath.section < self.situation.actions.count)
        {
            actions = [self.situation.actions objectAtIndex:newIndexPath.section];
            
            if (newIndexPath.row < actions.count)
            {
                [self performSelector:@selector(playActionSound:) withObject:[actions actionSoundAtIndex: newIndexPath.row] afterDelay: 0.2];
            }
        }
    }
        
    /*
    unsigned int currStrikeStyle = [[typAttrs objectForKey:NSStrikethroughStyleAttributeName] unsignedIntValue];

    
    NSAttributedString* strikedText = [NSAttributedString initWithString:cell.textLabel.text attributes:[NSDictionary dictionaryWithObjectsAndKeys:[NSNumber numberWithInteger: NSStrikethroughStyleAttributeName, nil]]];
    cell.textLabel.text = strikedText;
    */
    
    [self.tableView scrollToRowAtIndexPath:newIndexPath atScrollPosition:UITableViewScrollPositionMiddle animated:YES];
}

-(IBAction)onGetBack:(id)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    [self.navigationController popViewControllerAnimated:YES];
}

- (void)viewDidLoad
{
    UIImageView *imageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"ipad-BG@2x.png"]];
    
    self.tableView.backgroundView = imageView;

    self.currentRow = 0;
    
    self.titleButton.titleLabel.textAlignment = UITextAlignmentCenter;
    [super viewDidLoad];
}

- (void)viewWillDisappear:(BOOL)animated
{
    if (self.alert != nil)
    {
        [self.alert dismissWithClickedButtonIndex:0 animated:YES];
    }
    self.alert = nil;
    
    [AppDelegate stopPlaySound];
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    
    self.resetButton = nil;
    self.titleButton = nil;
    
    [self.timer invalidate];
    self.timer = nil;
    
    self.alert = nil;
    self.situation = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return YES;
}

-(IBAction)onShowDetails:(id)sender
{
    NSString *titleForPage = @"";
    NSArray *tokens = [self.situation.title componentsSeparatedByString:@"п. "];
    
    if (tokens.count > 1)
    {
        tokens = [[tokens objectAtIndex:1] componentsSeparatedByString:@")"];
        
        if (tokens.count > 1)
        {
            tokens = [[tokens objectAtIndex:0] componentsSeparatedByString:@"."];
            
            if (tokens.count > 1)
            {
                titleForPage = [NSString stringWithFormat:@"%@.%@.", [tokens objectAtIndex:0], [tokens objectAtIndex:1]];
            }
        }
    }
    
    [self resolveDocumentByLink:self.situation.title];
    
    if (self.docFileName != nil)
    {
        ReaderDocument *document = [ReaderDocument withDocumentFilePath:self.docFileName password:nil];
        
        if (document != nil) // Must have a valid ReaderDocument object in order to proceed with things
        {
            ReaderViewController *readerViewController = [[ReaderViewController alloc] initWithReaderDocument:document];
            
            readerViewController.delegate = self; // Set the ReaderViewController delegate to self
            readerViewController.modalTransitionStyle = UIModalTransitionStyleCrossDissolve;
            readerViewController.modalPresentationStyle = UIModalPresentationFullScreen;
            
            [self presentModalViewController:readerViewController animated:YES];
            
            [readerViewController showDocumentSubtitle:titleForPage];
        }
    }
}

- (void)dismissReaderViewController:(ReaderViewController *)viewController
{    
    [[[AppDelegate appDelegate] tapPlayer] play];
    
	//[self.navigationController popViewControllerAnimated:YES];
    [self dismissModalViewControllerAnimated:YES];
    
    self.docFileName = nil;
}

- (void)resolveDocumentByLink: (NSString*) link
{
    NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
    
    if ([paths count] > 0)
    {
        NSError *error = nil;  
        NSFileManager *fileManager = [NSFileManager defaultManager];
        
        // Print out the path to verify we are in the right place
        NSString *directory = [paths objectAtIndex:0];
        
        // For each file in the directory, create full path and delete the file
        for (NSString *file in [fileManager contentsOfDirectoryAtPath:directory error:&error])
        {    
            NSString *filePath = [directory stringByAppendingPathComponent:file];
            
            if ([file rangeOfString:@".pdf"].location != NSNotFound)
            {
                if (([link rangeOfString:@"РПП"].location != NSNotFound) &&
                    ([file rangeOfString:@"РПП"].location != NSNotFound))
                {
                    self.docFileName = filePath;
                    
                    break;
                }
                else if (([link rangeOfString:@"МТВ"].location != NSNotFound) && 
                         ([link rangeOfString:@"РЛЭ"].location != NSNotFound) &&
                         ([file rangeOfString:@"МТВ"].location != NSNotFound) && 
                         ([file rangeOfString:@"РЛЭ"].location != NSNotFound))
                         {
                             self.docFileName = filePath;
                             break;
                         }
                else if (([link rangeOfString:@"РЛЭ"].location != NSNotFound) &&
                        ([file rangeOfString:@"РЛЭ"].location != NSNotFound))
                {
                    self.docFileName = filePath;
                    break;
                }
            }
        }
    }
}

@end
