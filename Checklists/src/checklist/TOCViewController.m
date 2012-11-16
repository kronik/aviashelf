//
//	TOCViewController.m
//	Reader v2.5.4
//
//	Created by Julius Oklamcak on 2011-09-01.
//	Copyright © 2011-2012 Julius Oklamcak. All rights reserved.
//
//	Permission is hereby granted, free of charge, to any person obtaining a copy
//	of this software and associated documentation files (the "Software"), to deal
//	in the Software without restriction, including without limitation the rights to
//	use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
//	of the Software, and to permit persons to whom the Software is furnished to
//	do so, subject to the following conditions:
//
//	The above copyright notice and this permission notice shall be included in all
//	copies or substantial portions of the Software.
//
//	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
//	OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
//	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
//	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
//	WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
//	CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
//

#import "ReaderConstants.h"
#import "TOCViewController.h"
#import "ReaderThumbRequest.h"
#import "ReaderThumbCache.h"
#import "ReaderDocument.h"
#import "OutlinesItem.h"

#import <QuartzCore/QuartzCore.h>

@implementation TOCViewController

#pragma mark Constants

#define TOOLBAR_HEIGHT 44.0f

#define PAGE_THUMB_SMALL 160
#define PAGE_THUMB_LARGE 256

#pragma mark Properties

@synthesize delegate;

#pragma mark UIViewController methods

- (id)initWithReaderDocument:(ReaderDocument *)object
{
#ifdef DEBUGX
	NSLog(@"%s", __FUNCTION__);
#endif

	id thumbs = nil; // TOCViewController object

	if ((object != nil) && ([object isKindOfClass:[ReaderDocument class]]))
	{
		if ((self = [super initWithNibName:nil bundle:nil])) // Designated initializer
		{
			tocList = [[[NSMutableArray alloc] init] retain] ; // Bookmarked pages

			document = [object retain]; // Retain the ReaderDocument object for our use

			thumbs = self; // Return an initialized TOCViewController object
		}
	}

	return thumbs;
}

/*
- (void)loadView
{
#ifdef DEBUGX
	NSLog(@"%s", __FUNCTION__);
#endif

	// Implement loadView to create a view hierarchy programmatically, without using a nib.
}
*/

- (void)viewDidLoad
{
#ifdef DEBUGX
	NSLog(@"%s %@", __FUNCTION__, NSStringFromCGRect(self.view.bounds));
#endif

	[super viewDidLoad];

	NSAssert(!(delegate == nil), @"delegate == nil");

	NSAssert(!(document == nil), @"ReaderDocument == nil");

	self.view.backgroundColor = [UIColor scrollViewTexturedBackgroundColor];

	CGRect viewRect = self.view.bounds; // View controller's view bounds

	NSString *toolbarTitle = [document.fileName stringByDeletingPathExtension];

	CGRect toolbarRect = viewRect; toolbarRect.size.height = TOOLBAR_HEIGHT;

	mainToolbar = [[TOCMainToolbar alloc] initWithFrame:toolbarRect title:toolbarTitle]; // At top

	mainToolbar.delegate = self;

	[self.view addSubview:mainToolbar];

	CGRect thumbsRect = viewRect; UIEdgeInsets insets = UIEdgeInsetsZero;

	if ([UIDevice currentDevice].userInterfaceIdiom == UIUserInterfaceIdiomPad)
	{
		thumbsRect.origin.y += TOOLBAR_HEIGHT; thumbsRect.size.height -= TOOLBAR_HEIGHT;
	}
	else // Set UIScrollView insets for non-UIUserInterfaceIdiomPad case
	{
		insets.top = TOOLBAR_HEIGHT;
	}
    
    CFURLRef docURLRef = (CFURLRef)document.fileURL; // CFURLRef from NSURL
    CGPDFDocumentRef pdfDocument = CGPDFDocumentCreateX(docURLRef, document.password);
    
    CGPDFDictionaryRef catalog;
	catalog = CGPDFDocumentGetCatalog(pdfDocument);

	//Root of the outlines
	CGPDFDictionaryRef outlines;
	OutlinesItem *outlinesRoot;
    
	//Setup the outlines tree with iteration
	if (CGPDFDictionaryGetDictionary(catalog, "Outlines", &outlines))
	{        
		CGPDFDictionaryRef first;
		if (CGPDFDictionaryGetDictionary(outlines, "First", &first) == TRUE)
		{
			outlinesRoot = [[OutlinesItem alloc] init];
            //[outlinesRoot createOutline:pdfDocument];
            
			[outlinesRoot setupNode:outlines ofPdfDoc:pdfDocument level:0 previousPage:1 toc:tocList];
            
            //[self printOutlines:tocList];
		}
		else
		{
			outlinesRoot = nil;
		}
	}
	else
	{
		outlinesRoot = nil;
	}

	tocTable = [[UITableView alloc] initWithFrame:thumbsRect style:UITableViewStylePlain]; // Rest
	tocTable.delegate = self;
    tocTable.dataSource = self;
    tocTable.backgroundColor = [UIColor clearColor];
    tocTable.separatorStyle = UITableViewCellSeparatorStyleNone;
    [tocTable setAutoresizingMask:UIViewAutoresizingFlexibleWidth|UIViewAutoresizingFlexibleHeight];

	[self.view insertSubview:tocTable belowSubview:mainToolbar];
}

- (int)getDocumentPageByTitle: (NSString*)subTitle
{
    int resultPage = 1;
    
    for (OutlinesItem *outline in tocList)
    {
        NSArray *tokens = [outline.pageTitle componentsSeparatedByString:@" "];
        
        if ([[tokens objectAtIndex:0] isEqualToString:subTitle] == YES)
        {
            resultPage = outline.pageNumber;
            break;
        }
    }
    
    return resultPage;
}

- (void)printOutlines: (NSArray*)toc
{
    for (int i=0; i<tocList.count; i++)
    {
        OutlinesItem *item = [toc objectAtIndex:i];
        
        NSLog(@"Level: %d -> %@ %d\n", item.titleLevel, item.pageTitle, item.pageNumber);
    }
}

- (void)viewWillAppear:(BOOL)animated
{
#ifdef DEBUGX
	NSLog(@"%s %@", __FUNCTION__, NSStringFromCGRect(self.view.bounds));
#endif

	[super viewWillAppear:animated];

    [tocTable reloadData];
}

- (void)viewDidAppear:(BOOL)animated
{
#ifdef DEBUGX
	NSLog(@"%s %@", __FUNCTION__, NSStringFromCGRect(self.view.bounds));
#endif

	[super viewDidAppear:animated];
}

- (void)viewWillDisappear:(BOOL)animated
{
#ifdef DEBUGX
	NSLog(@"%s %@", __FUNCTION__, NSStringFromCGRect(self.view.bounds));
#endif

	[super viewWillDisappear:animated];
}

- (void)viewDidDisappear:(BOOL)animated
{
#ifdef DEBUGX
	NSLog(@"%s %@", __FUNCTION__, NSStringFromCGRect(self.view.bounds));
#endif

	[super viewDidDisappear:animated];
}

- (void)viewDidUnload
{
#ifdef DEBUGX
	NSLog(@"%s", __FUNCTION__);
#endif

	[tocTable release], tocTable = nil;

	[mainToolbar release], mainToolbar = nil;

	[super viewDidUnload];
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
#ifdef DEBUGX
	NSLog(@"%s (%d)", __FUNCTION__, interfaceOrientation);
#endif

	return YES;
}

- (void)willRotateToInterfaceOrientation:(UIInterfaceOrientation)toInterfaceOrientation duration:(NSTimeInterval)duration
{
#ifdef DEBUGX
	NSLog(@"%s %@ (%d)", __FUNCTION__, NSStringFromCGRect(self.view.bounds), toInterfaceOrientation);
#endif
}

- (void)willAnimateRotationToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation duration:(NSTimeInterval)duration
{
#ifdef DEBUGX
	NSLog(@"%s %@ (%d)", __FUNCTION__, NSStringFromCGRect(self.view.bounds), interfaceOrientation);
#endif
}

- (void)didRotateFromInterfaceOrientation:(UIInterfaceOrientation)fromInterfaceOrientation
{
#ifdef DEBUGX
	NSLog(@"%s %@ (%d to %d)", __FUNCTION__, NSStringFromCGRect(self.view.bounds), fromInterfaceOrientation, self.interfaceOrientation);
#endif

	//if (fromInterfaceOrientation == self.interfaceOrientation) return;
}

- (void)didReceiveMemoryWarning
{
#ifdef DEBUGX
	NSLog(@"%s", __FUNCTION__);
#endif

	[super didReceiveMemoryWarning];
}

- (void)dealloc
{
#ifdef DEBUGX
	NSLog(@"%s", __FUNCTION__);
#endif

	[tocList release], tocList = nil;

	[tocTable release], tocTable = nil;

	[mainToolbar release], mainToolbar = nil;

	[document release], document = nil;

	[super dealloc];
}

#pragma mark ThumbsMainToolbarDelegate methods

- (void)tappedInToolbar:(TOCMainToolbar *)toolbar doneButton:(UIButton *)button
{
#ifdef DEBUGX
	NSLog(@"%s", __FUNCTION__);
#endif

	[delegate dismissTOCViewController:self]; // Dismiss thumbs display
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    return 1;
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    return tocList.count;
}

- (BOOL)tableView:(UITableView *)tableView canPerformAction:(SEL)action forRowAtIndexPath:(NSIndexPath *)indexPath withSender:(id)sender
{
    return NO;//(action == @selector(copy:));
}

- (UITableViewCell *)tableView:(UITableView *)tableView cellForRowAtIndexPath:(NSIndexPath *)indexPath
{
    static NSString *kCellID = @"subcellID";
	
	UITableViewCell *cell = [tableView dequeueReusableCellWithIdentifier:kCellID];
	if (cell == nil)
	{
		cell = [[UITableViewCell alloc] initWithStyle:UITableViewCellStyleValue1 reuseIdentifier:kCellID];
        cell.selectionStyle = UITableViewCellSelectionStyleNone;
        cell.accessoryType = UITableViewCellAccessoryNone;
        cell.textLabel.textColor = [UIColor whiteColor];
        cell.textLabel.font = [UIFont fontWithName:@"Helvetica" size:20.0]; 
        //cell.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];
        cell.textLabel.numberOfLines = 3;

        //cell.textLabel.font = [UIFont boldSystemFontOfSize:20];
	}

    OutlinesItem *outline = [tocList objectAtIndex:indexPath.row];
    
    if (outline.titleLevel == 0)
    {
        cell.textLabel.font = [UIFont boldSystemFontOfSize: 24]; 
        cell.textLabel.text = outline.pageTitle;
    }
    else
    {
        cell.textLabel.font = [UIFont fontWithName:@"Helvetica" size:20.0]; 
        cell.textLabel.text = [NSString stringWithFormat:@"      %@", outline.pageTitle];
    }
    cell.tag = outline.pageNumber;
    
    //cell.detailTextLabel.text = [NSString stringWithFormat:@"%d", outline.pageNumber];
    
	return cell;
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 60.0;
}

-(void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{
    OutlinesItem *outline = [tocList objectAtIndex:indexPath.row];    
    
    [delegate TOCViewController:self gotoPage:outline.pageNumber]; // Show the selected page
	[delegate dismissTOCViewController:self]; // Dismiss thumbs display
}

@end

