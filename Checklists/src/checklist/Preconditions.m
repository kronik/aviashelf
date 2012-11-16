//
//  Precondition.m
//  checklist
//
//  Created by dima on 4/4/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "Preconditions.h"

@interface Preconditions()

@property (nonatomic, strong) NSMutableArray *preconditions;

@end

@implementation Preconditions

@synthesize header = _header;
@synthesize preconditions = _preconditions;

-(NSMutableArray*)preconditions
{
    if (_preconditions == nil)
    {
        _preconditions = [[NSMutableArray alloc] init];
    }
    return _preconditions;
}

-(void)addPrecondition:(NSString *)precondition
{
    [self.preconditions addObject:precondition];
}

-(NSString*)preconditionAtIndex: (int)idx
{
    return [self.preconditions objectAtIndex:idx];
    
}

-(int)count
{
    return self.preconditions.count;
}

-(void)dealloc
{
    [self.preconditions removeAllObjects];
    
    self.header = nil;
}

- (id)initWithCoder:(NSCoder *)aDecoder 
{    
    if (self = [super init]) 
    {
        _header = [aDecoder decodeObjectForKey:@"header"];
        _preconditions = [aDecoder decodeObjectForKey:@"preconditions"];
    }            
    return self;
}

- (void)encodeWithCoder:(NSCoder *)aCoder 
{    
    [aCoder encodeObject:_header forKey:@"header"];
    [aCoder encodeObject:_preconditions forKey:@"preconditions"];
}

@end
