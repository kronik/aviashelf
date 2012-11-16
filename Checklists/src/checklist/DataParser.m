//
//  DataParser.m
//  checklist
//
//  Created by dima on 4/5/12.
//  Copyright (c) 2012 __MyCompanyName__. All rights reserved.
//

#import "DataParser.h"
#import "Situation.h"
#import "Preconditions.h"
#import "Actions.h"

@interface DataParser()

@property (nonatomic, strong) Actions *currentActions;
@property (nonatomic, strong) Preconditions *currentPreconditions;
@property (nonatomic, strong) Situation *currentSituation;

@end

@implementation DataParser

@synthesize currentActions = _currentActions;
@synthesize currentSituation = _currentSituation;
@synthesize currentPreconditions = _currentPreconditions;
@synthesize delegate = _delegate;

@synthesize situations = _situations;

-(NSMutableArray*)situations
{
    if (_situations == nil)
    {
        _situations = [[NSMutableArray alloc] init];
    }
    return _situations;
}

-(void)parseFile: (NSString*)dataFile
{
    NSString *documentsDirectory = [NSSearchPathForDirectoriesInDomains(NSDocumentDirectory, NSUserDomainMask, YES) objectAtIndex:0];
    NSString *path = [documentsDirectory stringByAppendingPathComponent:[NSString stringWithFormat:@"%@.xml", dataFile]];

    if( [[NSFileManager defaultManager] fileExistsAtPath: path] == NO)
    {
        [[[UIAlertView alloc] initWithTitle:@"Ошибка!" message:[NSString stringWithFormat: @"Отсутствует файл: <%@>", dataFile] delegate:nil cancelButtonTitle:@"OK" otherButtonTitles:nil, nil] show];
        return;
    }
    
    NSData *data = [NSData dataWithContentsOfFile: path];
    NSXMLParser *parser = [[NSXMLParser alloc] initWithData: data];
    
    self.currentActions = nil;
    self.currentPreconditions = nil;
    self.currentSituation = nil;
    
    [self.situations removeAllObjects];
    
    [parser setDelegate:self];
    [parser parse];
}

- (void)parser:(NSXMLParser *)parser didStartElement:(NSString *)elementName namespaceURI:(NSString *)namespaceURI qualifiedName:(NSString *)qualifiedName attributes:(NSDictionary *)attributeDict
{
    if ([elementName isEqualToString:@"situation"])
    {        
        if (self.currentSituation != nil)
        {
            //TODO: Save previous situation
        }
        
        self.currentSituation = [[Situation alloc] init];
        self.currentSituation.title = [attributeDict valueForKey:@"title"];
        
        [self.situations addObject:self.currentSituation];
        
        self.currentActions = nil;
        self.currentPreconditions = nil;
    }
    
    if ([elementName isEqualToString:@"preconds"])
    {
        if (self.currentPreconditions != nil)
        {
            //TODO: Save previous preconds
        }
        
        self.currentPreconditions = [[Preconditions alloc] init];
        self.currentPreconditions.header = [attributeDict valueForKey:@"txt"];
        
        [self.currentSituation.preconditions addObject:self.currentPreconditions];
    }
    
    if ([elementName isEqualToString:@"precond"])
    {        
        [self.currentPreconditions addPrecondition: [attributeDict valueForKey:@"txt"]];
    }
    
    if ([elementName isEqualToString:@"actions"])
    {
        if (self.currentActions != nil)
        {
            //TODO: Save previous actions
        }
        
        self.currentActions = [[Actions alloc] init];
        self.currentActions.header = [attributeDict valueForKey:@"txt"];
        
        [self.currentSituation.actions addObject:self.currentActions];
    }
    
    if ([elementName isEqualToString:@"action"])
    {
        [self.currentActions addAction: [attributeDict valueForKey:@"txt"]];
    }
}

- (void)parserDidEndDocument:(NSXMLParser *)parser
{
    self.currentActions = nil;
    self.currentPreconditions = nil;
    self.currentSituation = nil;
    
    [self.delegate dataParserDidFinish:self situations:self.situations];
}

@end
