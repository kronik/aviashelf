//
//  DataParser.h
//  checklist
//
//  Created by dima on 4/5/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

@class DataParser;

@protocol DataParserDelegate
- (void)dataParserDidFinish:(DataParser *)dataParser situations:(NSArray*)situations;
@end

@interface DataParser : NSObject<NSXMLParserDelegate>

-(void)parseFile: (NSString*)dataFile;

@property (nonatomic, strong) NSMutableArray *situations;
@property (assign, nonatomic) IBOutlet id <DataParserDelegate> delegate;

@end
