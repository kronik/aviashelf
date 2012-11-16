//
//  Situation.h
//  checklist
//
//  Created by dima on 4/4/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface Situation : NSObject <NSCoding>

@property (nonatomic, strong) NSString *title;
@property (nonatomic, strong) NSMutableArray *preconditions;
@property (nonatomic, strong) NSMutableArray *actions;

@end
