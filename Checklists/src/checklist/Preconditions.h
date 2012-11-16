//
//  Precondition.h
//  checklist
//
//  Created by dima on 4/4/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Preconditions : NSObject <NSCoding>

@property (nonatomic, strong) NSString *header;

-(void)addPrecondition: (NSString*)precondition;
-(NSString*)preconditionAtIndex: (int)idx;
-(int)count;

@end
