//
//  Actions.m
//  checklist
//
//  Created by dima on 4/4/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Actions.h"

#define ACTIONS_COUNT 16

@interface Actions()

@property (nonatomic, strong) NSMutableArray *actions;
@property (nonatomic) BOOL *actionMarkers;

@end

@implementation Actions

@synthesize header = _header;
@synthesize actions = _actions;
@synthesize actionMarkers = _actionMarkers;

-(NSMutableArray*)actions
{
    if (_actions == nil)
    {
        _actions = [[NSMutableArray alloc] init];
    }
    return _actions;
}

-(BOOL*)actionMarkers
{
    if (_actionMarkers == nil)
    {
       _actionMarkers = (BOOL*)malloc(sizeof(BOOL) * ACTIONS_COUNT);
        memset(_actionMarkers, 0, sizeof(BOOL) * ACTIONS_COUNT);
    }
    return _actionMarkers;
}


-(void)addAction: (NSString*)action
{
    [self.actions addObject:action];
}

-(NSString*)actionAtIndex: (int)idx
{
    return [self.actions objectAtIndex:idx];

}

-(int)count
{
    return self.actions.count;
}

-(BOOL)isActionAtIdxDone: (int)idx
{
    if (idx >= ACTIONS_COUNT)
    {
        NSLog(@"Index out range");
        return NO;
    }
    return self.actionMarkers[idx];
}

-(void)setActionAtIdxDone: (int)idx
{
    if (idx >= ACTIONS_COUNT)
    {
        NSLog(@"Index out range");
    }
    
    self.actionMarkers[idx] = YES;
}

-(void)setActionAtIdxUndone: (int)idx
{
    if (idx >= ACTIONS_COUNT)
    {
        NSLog(@"Index out range");
    }
    
    self.actionMarkers[idx] = NO;
}

-(void)dealloc
{
    [self.actions removeAllObjects];
    
    self.header = nil;
    
    if (_actionMarkers != nil)
    {
        free(_actionMarkers);
    }
}

- (id)initWithCoder:(NSCoder *)aDecoder
{    
    if (self = [super init]) 
    {
        _header = [aDecoder decodeObjectForKey:@"header"];
        _actions = [aDecoder decodeObjectForKey:@"actions"];        
    }            
    return self;
}

- (void)encodeWithCoder:(NSCoder *)aCoder 
{    
    [aCoder encodeObject:_header forKey:@"header"];
    [aCoder encodeObject:_actions forKey:@"actions"];
}

@end
