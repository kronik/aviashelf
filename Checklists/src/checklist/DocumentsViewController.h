//
//  DocumentsViewController.h
//  checklist
//
//  Created by kronik on 4/15/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <UIKit/UIKit.h>
#import "ReaderViewController.h"
#import <DropboxSDK/DropboxSDK.h>
#import "Reachability.h"

@interface DocumentsViewController : UITableViewController<ReaderViewControllerDelegate, DBRestClientDelegate, UIAlertViewDelegate>

- (IBAction)onGetBack:(id)sender;
- (IBAction)reloadDocumentsList:(id)sender;

@end
