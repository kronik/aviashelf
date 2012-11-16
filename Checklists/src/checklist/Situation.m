//
//  Situation.m
//  checklist
//
//  Created by dima on 4/4/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Situation.h"

@implementation Situation

@synthesize actions = _actions;
@synthesize preconditions = _preconditions;
@synthesize title = _title;

-(NSMutableArray*)preconditions
{
    if (_preconditions == nil)
    {
        _preconditions = [[NSMutableArray alloc] init];
    }
    return _preconditions;
}

-(NSMutableArray*)actions
{
    if (_actions == nil)
    {
        _actions = [[NSMutableArray alloc] init];
    }
    return _actions;
}

-(void)dealloc
{
    [self.preconditions removeAllObjects];
    [self.actions removeAllObjects];
    self.title = nil;
}

- (id)initWithCoder:(NSCoder *)aDecoder
{    
    if (self = [super init]) 
    {
        _title = [aDecoder decodeObjectForKey:@"title"];
        _actions = [aDecoder decodeObjectForKey:@"actions"];
        _preconditions = [aDecoder decodeObjectForKey:@"preconditions"];
        
    }            
    return self;
}

- (void)encodeWithCoder:(NSCoder *)aCoder 
{    
    [aCoder encodeObject:_title forKey:@"title"];
    [aCoder encodeObject:_actions forKey:@"actions"];
    [aCoder encodeObject:_preconditions forKey:@"preconditions"];
}

@end
