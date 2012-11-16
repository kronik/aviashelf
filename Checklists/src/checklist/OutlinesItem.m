//
//  OutlinesItem.m
//  PDFReader_Q2D
//
//  Created by Gu Lei on 10-4-3.
//  Copyright 2010 __MyCompanyName__. All rights reserved.
//

#import "OutlinesItem.h"


@implementation OutlinesItem

@synthesize pageNumber = pageNumber;
@synthesize pageTitle = pageTitle;
@synthesize titleLevel = titleLevel;

- (Boolean) setupNode: (CGPDFDictionaryRef) nodeDictionary ofPdfDoc:(CGPDFDocumentRef)pdfDoc level:(int)level  previousPage: (int) previousPageNumber toc: (NSMutableArray*)toc
{
    titleLevel = level;
    
	//Set some info
	int levelStep = [self setInfo:nodeDictionary ofPdfDoc:pdfDoc toc:toc];
    
    if (self.pageNumber == -1)
    {
        self.pageNumber = previousPageNumber;
    }
	
	//First, check the next brother node of the level outlines
	CGPDFDictionaryRef next;
	if (CGPDFDictionaryGetDictionary(nodeDictionary, "First", &next) == TRUE)
	{
		OutlinesItem *tempItem = [[OutlinesItem alloc] init];
		[tempItem setupNode:next ofPdfDoc:pdfDoc level:level+levelStep previousPage:self.pageNumber toc:toc];
	}
	
	//Second, check the first child node of the level outlines
	CGPDFDictionaryRef first;
	if (CGPDFDictionaryGetDictionary(nodeDictionary, "Next", &first) == TRUE)
	{
		OutlinesItem *tempItem = [[OutlinesItem alloc] init];
		[tempItem setupNode:first ofPdfDoc:pdfDoc level:level previousPage:self.pageNumber toc:toc]; 
	}
	else
	{
	}

	return TRUE;
}

// Get Page Number from an array
- (int) getPageNumberFromArray:(CGPDFArrayRef)array ofPdfDoc:(CGPDFDocumentRef)pdfDoc withNumberOfPages:(int)numberOfPages
{
    pageNumber = -1;

    // Page number reference is the first element of array (el 0)
    CGPDFDictionaryRef pageDic;
    CGPDFArrayGetDictionary(array, 0, &pageDic);
    
    // page searching
    for (int p=1; p<=numberOfPages; p++)
    {
        CGPDFPageRef page = CGPDFDocumentGetPage(pdfDoc, p);
        if (CGPDFPageGetDictionary(page) == pageDic)
        {
            pageNumber = p;
            break;
        }
    }
    
    return pageNumber;
}

// Get page number from an outline. Only support "Dest" and "A" entries
- (int) getPageNumber:(CGPDFDictionaryRef)node ofPdfDoc:(CGPDFDocumentRef)pdfDoc withNumberOfPages:(int)numberOfPages
{
    pageNumber = -1;
    
    CGPDFArrayRef destArray;
    CGPDFDictionaryRef dicoActions;
    if(CGPDFDictionaryGetArray(node, "Dest", &destArray))
    {        
        pageNumber = [self getPageNumberFromArray:destArray ofPdfDoc:pdfDoc withNumberOfPages:numberOfPages];
    }
    else if(CGPDFDictionaryGetDictionary(node, "A", &dicoActions))
    {        
        const char * typeOfActionConstChar;
        CGPDFDictionaryGetName(dicoActions, "S", &typeOfActionConstChar);
        
        NSString * typeOfAction = [NSString stringWithUTF8String:typeOfActionConstChar];
        if([typeOfAction isEqualToString:@"GoTo"]) // only support "GoTo" entry. See PDF spec p653
        {            
            CGPDFArrayRef dArray;
            if(CGPDFDictionaryGetArray(dicoActions, "D", &dArray)) 
            {                
                pageNumber = [self getPageNumberFromArray:dArray ofPdfDoc:pdfDoc withNumberOfPages:numberOfPages];
            }
        }
    }
    
    return pageNumber;
}

- (int) setInfo: (CGPDFDictionaryRef) nodeDictionary ofPdfDoc:(CGPDFDocumentRef)pdfDoc toc: (NSMutableArray*)toc
{	
	int levelStep = 0;
    
	//Set title
	CGPDFStringRef outlinesTitleRef;

	if (CGPDFDictionaryGetString(nodeDictionary, "Title", &outlinesTitleRef) == TRUE)
	{
        pageTitle = (NSString *)CGPDFStringCopyTextString(outlinesTitleRef);
                
        int pagesCount = CGPDFDocumentGetNumberOfPages(pdfDoc);
        pageNumber = [self getPageNumber:nodeDictionary ofPdfDoc:pdfDoc withNumberOfPages:pagesCount];
        
        levelStep = 1;
        
        /*
        for (int i=1; i<pagesCount; i++) 
        {
            if (nodeDictionary == CGPDFPageGetDictionary(CGPDFDocumentGetPage(pdfDoc, i)))
            {
                pageNumber = i;
            }
        }
         */
		
        //For debug
        //NSLog(@"Level: %d -> %@ %d from %d\n", titleLevel, pageTitle, pageNumber, pagesCount);
        
        [toc addObject:self];
	}
    
    return levelStep;
}

/*
-(void)createOutline: (CGPDFDocumentRef)thePDFDocRef
{    
    NSMutableArray *cataLogTitles = [NSMutableArray array];
    
    CGPDFDictionaryRef catalogDictionary = CGPDFDocumentGetCatalog(thePDFDocRef);
    
    CGPDFDictionaryRef namesDictionary = NULL;
    if (CGPDFDictionaryGetDictionary(catalogDictionary, "Outlines", &namesDictionary)) {
        
        long int myCount;
        if (CGPDFDictionaryGetInteger(namesDictionary, "Count", &myCount)) {
            NSLog(@"destinationName:%ld", myCount);
        }
        
        CGPDFDictionaryRef myDic;
        if( CGPDFDictionaryGetDictionary(namesDictionary, "First", &myDic) )
        {
            CGPDFStringRef myTitle;
            if( CGPDFDictionaryGetString(myDic, "Title", &myTitle) )
            {
                
                NSString *tempStr = (NSString *)CGPDFStringCopyTextString(myTitle);
                
                NSLog(@"myTitle===:%@", tempStr);
                NSString *num = [self returnCatalogListNumber:myDic PDFDoc:thePDFDocRef];
                NSDictionary *_MyDic = [NSDictionary dictionaryWithObjectsAndKeys:
                                        tempStr, @"title",
                                        num, @"link",
                                        nil];
                
                [cataLogTitles addObject:_MyDic];
                
                NSLog(@"%@===", num);
                CGPDFDictionaryRef tempDic;
                tempDic = myDic;
                int i = 0;
                while ( i < myCount ) {
                    if( CGPDFDictionaryGetDictionary( tempDic , "Next", &tempDic) )
                    {
                        CGPDFStringRef tempTitle;
                        if( CGPDFDictionaryGetString(tempDic, "Title", &tempTitle) )
                        {
                            NSString *tempStr = (NSString *)CGPDFStringCopyTextString(tempTitle);
                            NSLog(@"myTitle:%@", tempStr);
                            
                            NSString *num = [self returnCatalogListNumber:tempDic PDFDoc:thePDFDocRef];
                            NSLog(@"%@", num);
                            
                            NSDictionary *_MyDic = [NSDictionary dictionaryWithObjectsAndKeys:
                                                    tempStr, @"title",
                                                    num, @"link",
                                                    nil];
                            [cataLogTitles addObject:_MyDic];
                            
                        }
                    }
                    
                    i++;
                }
                
            }
        }
    }
    NSLog(@"%@", cataLogTitles);
}

-(NSString *)returnCatalogListNumber:(CGPDFDictionaryRef)tempCGPDFDic PDFDoc:(CGPDFDocumentRef)tempCGPDFDoc
{
    //------
    CGPDFDictionaryRef destDic;
    if( CGPDFDictionaryGetDictionary(tempCGPDFDic, "A", &destDic ))
    {
        CGPDFArrayRef destArray;
        if( CGPDFDictionaryGetArray(destDic, "D", &destArray) )
        {
            NSInteger targetPageNumber = 0; // The target page number
            
            CGPDFDictionaryRef pageDictionaryFromDestArray = NULL; // Target reference
            
            if (CGPDFArrayGetDictionary(destArray, 0, &pageDictionaryFromDestArray) == true)
            {
                NSInteger pageCount = CGPDFDocumentGetNumberOfPages(tempCGPDFDoc);
                
                for (NSInteger pageNumber = 1; pageNumber <= pageCount; pageNumber++)
                {
                    CGPDFPageRef pageRef = CGPDFDocumentGetPage(tempCGPDFDoc, pageNumber);
                    
                    CGPDFDictionaryRef pageDictionaryFromPage = CGPDFPageGetDictionary(pageRef);
                    
                    if (pageDictionaryFromPage == pageDictionaryFromDestArray) // Found it
                    {
                        targetPageNumber = pageNumber; break;
                    }
                }
            }
            else // Try page number from array possibility
            {
                CGPDFInteger pageNumber = 0; // Page number in array
                
                if (CGPDFArrayGetInteger(destArray, 0, &pageNumber) == true)
                {
                    targetPageNumber = (pageNumber + 1); // 1-based
                }
            }
            
            //NSLog(@"%d====", targetPageNumber);
            
            if (targetPageNumber > 0) // We have a target page number
            {
                return [NSString stringWithFormat:@"%d", targetPageNumber];
            }
        }
        
    }
    
    return @"0";
    //------
}

- (OutlinesItem*)recursiveUpdateOutlines: (CGPDFDictionaryRef) outlineDic parent:(OutlinesItem*) parent level:(NSUInteger) level
{
    // update outline count
//    outlineCount++;
    OutlinesItem* item = [[OutlinesItem alloc] init];
    // Level
    //item.level = level;
    // Title
    CGPDFStringRef titleRef;
    if(CGPDFDictionaryGetString(outlineDic, "Title", &titleRef)) {
        title = CGPDFStringGetBytePtr(titleRef);
        // DEBUG
        printf("- %s\n", title);

    }
    
    parentItem = parent;
    
    if (parentItem != nil) {
        // Add to parent
        [parentItem->childs addObject:item];
        // Next
        CGPDFDictionaryRef nextDic;
        if (CGPDFDictionaryGetDictionary(outlineDic, "Next", &nextDic)) {
            [self recursiveUpdateOutlines:nextDic parent:parentItem level: level];
        }
    }
    // First child
    CGPDFDictionaryRef firstDic;
    if (CGPDFDictionaryGetDictionary(outlineDic, "First", &firstDic)) {
        [self recursiveUpdateOutlines:firstDic parent:item level: level + 1];
    }
    // Dest
    CGPDFStringRef destString;
    if(CGPDFDictionaryGetString(outlineDic, "Dest", &destString)) {
        const char* pchDest = (const char*)CGPDFStringGetBytePtr(destString);
        CGPDFDictionaryRef destDic;
        if(CGPDFDictionaryGetDictionary(outlineDic, pchDest, &destDic)) {
            NSLog(@"");
        }
        else {
            printf("Dest: %s\n", pchDest);
            //item.destString = [NSString stringWithUTF8String:pchDest];
        }
        
        
    } else {
        CGPDFDictionaryRef ADic;
        if (CGPDFDictionaryGetDictionary(outlineDic, "A", &ADic)) {
            const char* pchS;
            if (CGPDFDictionaryGetName(ADic, "S", &pchS)) {
                CGPDFArrayRef destArray;
                
                if (CGPDFDictionaryGetArray(ADic, "D", &destArray)) {
                    int count = CGPDFArrayGetCount(destArray);
                    switch (count) {
                        case 5:
                        {
                            // dest page
                            CGPDFDictionaryRef destPageDic;
                            if (CGPDFArrayGetDictionary(destArray, 0, &destPageDic)) {
                                int pageNumber = [self.pages indexOfObjectIdenticalTo:destPageDic];
                                item.page = pageNumber;
                            }
                            // x
                            CGPDFInteger x;
                            if (CGPDFArrayGetInteger(destArray, 2, &x)) {
                                item.x = x;
                            }
                            // y
                            CGPDFInteger y;
                            if (CGPDFArrayGetInteger(destArray, 3, &y)) {
                                item.y = y;
                            }
                            // z
                        }
                            break;
                        default:
                            NSLog(@"");
                            break;
                    }
                }
            }
        }
    }
    
    
    return item;
} 
*/

@end
