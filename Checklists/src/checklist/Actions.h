//
//  Actions.h
//  checklist
//
//  Created by dima on 4/4/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Actions : NSObject <NSCoding>

@property (nonatomic, strong) NSString *header;

-(void)addAction: (NSString*)action;
-(NSString*)actionAtIndex: (int)idx;
-(int)count;

-(BOOL)isActionAtIdxDone: (int)idx;
-(void)setActionAtIdxDone: (int)idx;
-(void)setActionAtIdxUndone: (int)idx;
@end
