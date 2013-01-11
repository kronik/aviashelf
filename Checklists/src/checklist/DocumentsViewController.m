//
//  DocumentsViewController.m
//  checklist
//
//  Created by kronik on 4/15/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "DocumentsViewController.h"
#import "AppDelegate.h"
#import "SVPullToRefresh.h"
#import <sys/socket.h>
#import <netinet/in.h>
#include <sys/xattr.h>
#import <SystemConfiguration/SystemConfiguration.h>

#define DB_APP_FOLDER @"/Аварийные карты/"
#define FILE_DATES_KEY @"FILE_DATES_KEY"

@interface DocumentsViewController ()

@property (strong, nonatomic) DBRestClient* restClient;
@property (nonatomic) BOOL working;
@property (strong, nonatomic) NSMutableArray *files;
@property (nonatomic, strong) NSMutableArray *documents;
@property (strong, nonatomic) NSMutableArray *dataFiles;
@property (strong, nonatomic) Reachability* hostReach;
@property (strong, nonatomic) NSMutableDictionary *fileDates;
@property (strong, nonatomic) NSMutableDictionary *filePaths;

- (IBAction)loadDocumentsList;

@end

@implementation DocumentsViewController

@synthesize documents = _documents;
@synthesize restClient = _restClient;
@synthesize working = _working;
@synthesize files = _files;
@synthesize dataFiles = _dataFiles;
@synthesize hostReach = _hostReach;
@synthesize fileDates = _fileDates;
@synthesize filePaths = _filePaths;

-(NSString*)getDateForDocument: (NSString*)docName
{
    NSString *docDate = nil;
    const char *tmp = [docName fileSystemRepresentation];
    
    docName = [NSString stringWithCString:tmp encoding:NSUTF8StringEncoding];
        
    @synchronized(self)
    {
        docDate = [[NSUserDefaults standardUserDefaults] stringForKey:docName];
        //docDate = [self.fileDates objectForKey:docName];
        
        if (docDate == nil)
        {
            docDate = @"";
        }
        else
        {
        }
    }
    return docDate;
}

-(void)setDateForDocument: (NSString*)docName docDate: (NSString*)lastModifiedDate
{
    @synchronized(self)
    {
        const char *tmp = [docName fileSystemRepresentation];
        
        docName = [NSString stringWithCString:tmp encoding:NSUTF8StringEncoding];

//        [self.fileDates setObject:lastModifiedDate forKey:docName];
//
//        NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
//        NSString *folderPath = [documentsDirectory stringByAppendingPathComponent:@"filedates.bin"];
//        
//        [self.fileDates writeToFile:folderPath atomically:YES];
        
        [[NSUserDefaults standardUserDefaults] setValue:lastModifiedDate forKey:docName];
        [[NSUserDefaults standardUserDefaults] synchronize];
//        
//        NSLog(@"Sync: %@", aaa?@"YES":@"NO");
    }
}

-(NSMutableDictionary*)fileDates
{
    if (_fileDates == nil)
    {
        NSUserDefaults *defaults = [NSUserDefaults standardUserDefaults];
        _fileDates = [defaults objectForKey: FILE_DATES_KEY];
        
        if (_fileDates != nil)
        {
            _fileDates = [_fileDates mutableCopy];
        }
        else
        {
            _fileDates = [[NSMutableDictionary alloc] init];
        }
    }
    return _fileDates;
}

-(NSMutableArray*)documents
{
    if (_documents == nil)
    {
        _documents = [[NSMutableArray alloc] init];
    }
    return _documents;
}

-(NSMutableArray*)files
{
    if (_files == nil)
    {
        _files = [[NSMutableArray alloc] init];
    }
    return _files;
}

-(NSMutableArray*)dataFiles
{
    if (_dataFiles == nil)
    {
        _dataFiles = [[NSMutableArray alloc] init];
    }
    return _dataFiles;
}

- (DBRestClient*)restClient 
{
    if (_restClient == nil) 
    {
        _restClient = [[DBRestClient alloc] initWithSession:[DBSession sharedSession]];
        _restClient.delegate = self;
    }
    return _restClient;
}

- (NSMutableDictionary *)filePaths
{
    if (_filePaths == nil)
    {
        _filePaths = [[NSMutableDictionary alloc] init];
    }
    return _filePaths;
}

-(IBAction)onGetBack:(id)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];
    [self.navigationController popViewControllerAnimated:YES];
}

- (void)loadBundleFilesWithExt: (NSString*)ext
{
    NSArray *bundleFiles = [[NSBundle mainBundle] pathsForResourcesOfType:ext inDirectory:nil];
        
    for (NSString *sourcePath in bundleFiles)
    {
        NSArray *tokens = [sourcePath componentsSeparatedByString:@"/"];
        NSString *fileName = tokens [tokens.count-1];
        
        self.filePaths [fileName] = sourcePath;
        
        if ([ext isEqualToString:@"pdf"])
        {
            [self.documents addObject: sourcePath];
        }
        else if ([ext isEqualToString:@"xml"])
        {
            [self.dataFiles addObject: sourcePath];
        }
    }
}

- (IBAction)loadDocumentsList;
{
    [self loadBundleFilesWithExt: @"pdf"];
    [self loadBundleFilesWithExt: @"xml"];
    
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
            
            if ([filePath rangeOfString:@".pdf"].location != NSNotFound)
            {
                [self.documents addObject:filePath];
            }
            
            if ([filePath rangeOfString:@".xml"].location != NSNotFound)
            {
                [self.dataFiles addObject:filePath];
            }
            
            self.filePaths [file] = filePath;
        }
    }
    
    [self.tableView reloadData];
}

- (IBAction)reloadDocumentsList:(id)sender
{
    [[[AppDelegate appDelegate] tapPlayer] play];

    if ([[DBSession sharedSession] isLinked] == YES && [self.hostReach currentReachabilityStatus] == ReachableViaWiFi &&
        self.files.count == 0)
    {
        [self.restClient loadMetadata:DB_APP_FOLDER]; 
    }
    else
    {
        [self.documents removeAllObjects];
        [self.dataFiles removeAllObjects];
        [self.filePaths removeAllObjects];
        
        [self loadDocumentsList];
    }
}

- (void) updateInterfaceWithReachability: (Reachability*) curReach
{
    NetworkStatus netStatus = [curReach currentReachabilityStatus];
//    BOOL connectionRequired= [curReach connectionRequired];

    if (netStatus == ReachableViaWiFi)
    {
        if ([[DBSession sharedSession] isLinked] == YES)
        {
            [self.restClient loadMetadata:DB_APP_FOLDER];        
        }
        else
        {
        }
    }
}

//Called by Reachability whenever status changes.
- (void) reachabilityChanged: (NSNotification* )note
{
	Reachability* curReach = [note object];
	NSParameterAssert([curReach isKindOfClass: [Reachability class]]);
	[self updateInterfaceWithReachability: curReach];
}

-(void)backPressed: (id)sender
{
    if (self.files.count == 0)
    {
        [self.navigationController popViewControllerAnimated: YES]; // or popToRoot... if required.
    }
    else 
    {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Внимание!" message:@"Происходит обновление документов приложения. Пожалуйста, дождитесь окончания процесса загрузки и обновления." delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil];
        [alert show];
    }
}

- (void)viewDidLoad
{
    UIBarButtonItem *btn = [[UIBarButtonItem alloc] initWithTitle:@"Назад" style:UIBarButtonItemStyleBordered target:self action:@selector(backPressed:)];
    btn.tintColor = [UIColor blueColor];
    self.navigationItem.leftBarButtonItem = btn;    

    /*
    [self.tableView addPullToRefreshWithActionHandler:^{
        
        [[[AppDelegate appDelegate] tapPlayer] play];
        
        [self.documents removeAllObjects];
        [self.dataFiles removeAllObjects];
        
        NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
        NSError *error = nil;  
        NSFileManager *fileManager = [NSFileManager defaultManager];
        
        if ([paths count] > 0)
        {
            // Print out the path to verify we are in the right place
            NSString *directory = [paths objectAtIndex:0];
            
            // For each file in the directory, create full path and delete the file
            for (NSString *file in [fileManager contentsOfDirectoryAtPath:directory error:&error])
            {    
                NSString *filePath = [directory stringByAppendingPathComponent:file];
                
                if ([filePath rangeOfString:@".pdf"].location != NSNotFound)
                {
                    [self.documents addObject:filePath];
                }
                
                if ([filePath rangeOfString:@".xml"].location != NSNotFound)
                {
                    [self.dataFiles addObject:filePath];
                }
            }
        }
        
        [self.tableView reloadData];
        
        [self.tableView.pullToRefreshView performSelector:@selector(stopAnimating) withObject:nil afterDelay:0.5];
    }];
    
    [self.tableView.pullToRefreshView triggerRefresh];
    */
    [self loadDocumentsList];
    UIImageView *imageView = [[UIImageView alloc] initWithImage:[UIImage imageNamed:@"LionDefault.png"]];
    
    self.tableView.backgroundView = imageView;
    
    [super viewDidLoad];
}

- (void)viewDidAppear:(BOOL)animated
{
    [[NSNotificationCenter defaultCenter] addObserver: self selector: @selector(reachabilityChanged:) name: kReachabilityChangedNotification object: nil];
    
    //Change the host name here to change the server your monitoring
	self.hostReach = [Reachability reachabilityWithHostName: @"www.dropbox.com"];
	[self.hostReach startNotifier];
	[self updateInterfaceWithReachability: self.hostReach];
    
    if ([[DBSession sharedSession] isLinked] == NO)
    {
        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Данное устройство не привязано" message:@"Хотите привязать его сейчас?" delegate:self cancelButtonTitle:@"Нет" otherButtonTitles:@"Да", nil];
        [alert show];
    }
}

- (void)viewDidDisappear:(BOOL)animated
{
    [[NSNotificationCenter defaultCenter] removeObserver:self name:kReachabilityChangedNotification object:nil];
    
    self.hostReach = nil;
    self.restClient = nil;
}

- (void) alertView:(UIAlertView *)alertView didDismissWithButtonIndex:(NSInteger)buttonIndex
{
    if ([[DBSession sharedSession] isLinked] == NO)
    {     
        if (buttonIndex != 0)
        {
            [[DBSession sharedSession] linkFromController:self];
        }
    }
    else
    {
        if (buttonIndex != 0)
        {
            NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES);
            NSString *directory = @"";
            
            if ([paths count] > 0)
            {
                // Print out the path to verify we are in the right place
                directory = [paths objectAtIndex:0];
            }
            
            for (DBMetadata *child in self.files)
            {                
                NSString *tmpFilePath = [directory stringByAppendingPathComponent:child.filename];
                [self.restClient loadFile: child.path intoPath:tmpFilePath];
            }
            
            self.tableView.userInteractionEnabled = NO;
            [self.tableView reloadData];
        }
        else
        {
            [self.files removeAllObjects];
            [self.tableView reloadData];
        }
    }
}

- (void)viewDidUnload
{
    [super viewDidUnload];
    
    [self.files removeAllObjects];
    self.files = nil;
    
    [self.documents removeAllObjects];
    self.documents = nil;
    
    [self.dataFiles removeAllObjects];
    self.dataFiles = nil;
    
    // Release any retained subviews of the main view.
    // e.g. self.myOutlet = nil;
}

- (BOOL)shouldAutorotateToInterfaceOrientation:(UIInterfaceOrientation)interfaceOrientation
{
    return YES;
}

- (NSInteger)numberOfSectionsInTableView:(UITableView *)tableView
{
    // Return the number of sections.
    
    if (self.files.count == 0)
    {
        return 2;
    }
    else 
    {
        return 1;
    }
}

- (NSInteger)tableView:(UITableView *)tableView numberOfRowsInSection:(NSInteger)section
{
    // Return the number of rows in the section.
    if (self.files.count == 0)
    {
        if (section == 0)
        {
            return self.documents.count;
        }
        else 
        {
            return  self.dataFiles.count;
        }
    }
    else
    {
        return self.files.count;
    }
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
        cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
        cell.textLabel.numberOfLines = 0;
        cell.textLabel.textColor = [UIColor darkGrayColor];
        cell.textLabel.font = [UIFont fontWithName:@"Helvetica" size:28.0]; 
        //cell.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];
        //cell.selectedBackgroundView.backgroundColor = [UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-element@2x.png"]];//[UIColor colorWithPatternImage:[UIImage imageNamed:@"ipad-list-item-selected@2x.png"]];
	}
    
    if (self.files.count == 0)
    {
        NSString *fileName = nil;
        
        if (indexPath.section == 0)
        {
            fileName = [self.documents objectAtIndex:indexPath.row];
            cell.accessoryType = UITableViewCellAccessoryDisclosureIndicator;
            cell.imageView.image = [UIImage imageNamed:@"document.png"];
        }
        else 
        {
            fileName = [self.dataFiles objectAtIndex:indexPath.row];
            cell.accessoryType = UITableViewCellAccessoryNone;
            cell.imageView.image = [UIImage imageNamed:@"file.png"];
        }
        
        NSArray *tokens = [fileName componentsSeparatedByString:@"/"];
        NSString *date = [self getDateForDocument:[tokens objectAtIndex:tokens.count - 1]];

        cell.textLabel.text = [tokens objectAtIndex:tokens.count - 1];
        
        if ([date isEqualToString:@""] == NO)
        {
            cell.detailTextLabel.text = [NSString stringWithFormat:@"Дата последнего обновления: %@", date];
        }
        else 
        {
            cell.detailTextLabel.text = @"";
        }
    }
    else 
    {
        DBMetadata* child = [self.files objectAtIndex:indexPath.row];
        
        cell.accessoryType = UITableViewCellAccessoryNone;
        cell.imageView.image = [UIImage imageNamed:@"download.png"];
        cell.textLabel.text = child.filename;
    }
	return cell;
}

-(CGFloat)tableView:(UITableView *)tableView heightForRowAtIndexPath:(NSIndexPath *)indexPath
{
    return 80;
}

- (void)tableView:(UITableView *)tableView didSelectRowAtIndexPath:(NSIndexPath *)indexPath
{   
    if (indexPath.section > 0 || self.files.count > 0)
    {
        return;
    }
    
    [[[AppDelegate appDelegate] tapPlayer] play];

    NSString *phrase = nil; // Document password (for unlocking most encrypted PDF files)
    NSString *filePath = [self.documents objectAtIndex:indexPath.row];
    ReaderDocument *document = [ReaderDocument withDocumentFilePath:filePath password:phrase];

	if (document != nil) // Must have a valid ReaderDocument object in order to proceed with things
	{
		ReaderViewController *readerViewController = [[ReaderViewController alloc] initWithReaderDocument:document];
        
		readerViewController.delegate = self; // Set the ReaderViewController delegate to self
        readerViewController.modalTransitionStyle = UIModalTransitionStyleCrossDissolve;
		readerViewController.modalPresentationStyle = UIModalPresentationFullScreen;
        
		[self presentModalViewController:readerViewController animated:YES];
    }
}

- (void)dismissReaderViewController:(ReaderViewController *)viewController
{    
    [[[AppDelegate appDelegate] tapPlayer] play];

	//[self.navigationController popViewControllerAnimated:YES];
    [self dismissModalViewControllerAnimated:YES];
}


#pragma mark DBRestClientDelegate methods

- (void)restClient:(DBRestClient*)client loadedMetadata:(DBMetadata*)metadata 
{
    NSArray* validExtensions = [NSArray arrayWithObjects:@"pdf", @"xml", nil];
    NSDateFormatter *formatter = [[NSDateFormatter alloc] init];
    
    [formatter setDateFormat:@"yyyy-MM-dd HH:mm:ss"];
    
    [self.files removeAllObjects];
    
    for (DBMetadata* child in metadata.contents) 
    {
        NSString* extension = [[child.path pathExtension] lowercaseString];
        if (!child.isDirectory && [validExtensions indexOfObject:extension] != NSNotFound) 
        {
            NSString *oldDate = [self getDateForDocument:child.filename];
            NSString *newDate = [formatter stringFromDate:child.lastModifiedDate];
            
            //NSLog(@"File <%@> Date1: %@ Date2: %@", child.filename, oldDate, newDate);
            
            if ([oldDate isEqualToString:newDate] == NO)
            {
                [self.files addObject:child];
            }
        }
    }
    
    if (self.files.count > 0)
    {
        [self.tableView reloadData];

        UIAlertView *alert = [[UIAlertView alloc] initWithTitle:@"Доступны обновленные документы" message:@"Хотите загрузить их сейчас?" delegate:self cancelButtonTitle:@"Нет" otherButtonTitles:@"Да", nil];
        [alert show];
    }
    
//    NSString *filename = @"NotesList.plist";
//    NSString *destDir = @"/";
//    NSArray *paths = NSSearchPathForDirectoriesInDomains(NSDocumentDirectory , NSUserDomainMask, YES);
//    NSString *documentsDir = [paths objectAtIndex:0];
//    NSString *address = [documentsDir stringByAppendingPathComponent:@"NotesList.plist"];
//    
//    [[self restClient] loadMetadata:@"/"];
//    
//    if([[NSFileManager defaultManager] fileExistsAtPath:address]) {
//        NSDictionary *dictionary = [[NSFileManager defaultManager] attributesOfItemAtPath:address error:&error];
//        NSDate *fileDate =[dictionary objectForKey:NSFileModificationDate];
//        
//        if ([[fileDate earlierDate:self.metaData.lastModifiedDate]isEqualToDate:fileDate]) {
//            [self.restClient loadFile:[NSString stringWithFormat: @"%@/%@", destDir, filename]
//                             intoPath:address];
//            NSLog(@"Downloading");
//        }
//        else if ([[self.metaData.lastModifiedDate earlierDate:fileDate] isEqualToDate:self.metaData.lastModifiedDate]) {
//            [[self restClient] uploadFile:filename toPath:destDir fromPath:address];
//            NSLog(@"Uploading");
//        }
//    }
    
    
    
//    
//    if (nil == fileDate || fileDate.timeIntervalSinceReferenceDate < self.metaData.lastModifiedDate.timeIntervalSinceReferenceDate)
//    {
//        [self.restClient loadFile:[NSString stringWithFormat: @"%@/%@", destDir, filename]
//                         intoPath:address];
//        NSLog(@"Downloading");
//    }
//    else if (nil != fileDate && fileDate.timeIntervalSinceReferenceDate > self.metaData.lastModifiedDate.timeIntervalSinceReferenceDate) {
//        [[self restClient] uploadFile:filename toPath:destDir fromPath:address];
//        NSLog(@"Uploading");
//    }
}

- (void)restClient:(DBRestClient *)client loadProgress:(CGFloat)progress forFile:(NSString *)destPath
{
    //NSLog(@"<%@> - %f", destPath, progress);
    int section = 0;
    int row = 0;
    
    NSArray *dstTokens = [destPath componentsSeparatedByString:@"/"];
    
    for (int i=0; i<self.files.count; i++)
    {
        DBMetadata *child = [self.files objectAtIndex:i];
        NSString *fileName = child.filename;
        NSArray *srcTokens = [fileName componentsSeparatedByString:@"/"];

        if ([[srcTokens objectAtIndex:srcTokens.count-1] isEqualToString: [dstTokens objectAtIndex:dstTokens.count-1]])
        {
            section = 0;
            row = i;
            break;
        }
    }
    
    NSIndexPath *indexPath = [NSIndexPath indexPathForRow:row inSection:section]; 
    UITableViewCell *cell = [self.tableView cellForRowAtIndexPath:indexPath];
    
    //cell.textLabel.text = [NSString stringWithFormat:@"%@ (%.01f%@)", [dstTokens objectAtIndex:dstTokens.count-1], progress * 100.0, @"%"];
    
    cell.detailTextLabel.text = [NSString stringWithFormat:@"Загружено: %.01f%@", progress * 100.0, @"%"];
    
    if (progress < 1.0)
    {
        cell.imageView.image = [UIImage imageNamed:@"download.png"];
        cell.accessoryType = UITableViewCellAccessoryNone;
    }
    
    [cell setNeedsLayout];
    [cell setNeedsDisplay];
}

- (void)restClient:(DBRestClient *)client loadedFile:(NSString *)destPath
{
    NSArray *tokens = [destPath componentsSeparatedByString:@"/"];
    NSDateFormatter *formatter = [[NSDateFormatter alloc] init];

    [formatter setDateFormat:@"yyyy-MM-dd HH:mm:ss"];

    for (DBMetadata *child in self.files)
    {
        if ([child.filename isEqualToString:[tokens objectAtIndex:tokens.count-1]])
        {
            NSString *newDate = [formatter stringFromDate:child.lastModifiedDate];
            
            self.filePaths [child.filename] = destPath;

            [self setDateForDocument:child.filename docDate:newDate];

            [self.files removeObject:child];
            
            if (self.files.count == 0)
            {
                self.tableView.userInteractionEnabled = YES;
            }
            
            [self.documents removeAllObjects];
            [self.dataFiles removeAllObjects];
            [self.filePaths removeAllObjects];
            
            [self loadDocumentsList];
            
            break;
        }
    }
}

- (void)restClient:(DBRestClient *)client loadFileFailedWithError:(NSError *)error
{
    NSLog(@"FAIL!: %@", error);
}

- (void)restClient:(DBRestClient*)client metadataUnchangedAtPath:(NSString*)path 
{
}

- (void)restClient:(DBRestClient*)client loadMetadataFailedWithError:(NSError*)error 
{
}

- (void)restClient:(DBRestClient*)client loadedThumbnail:(NSString*)destPath 
{
}

- (void)restClient:(DBRestClient*)client loadThumbnailFailedWithError:(NSError*)error 
{
}

@end
