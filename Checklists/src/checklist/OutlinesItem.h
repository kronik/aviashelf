//
//  OutlinesItem.h
//  PDFReader_Q2D
//
//  Created by Gu Lei on 10-4-3.
//  Copyright 2010 __MyCompanyName__. All rights reserved.
//

#import <Foundation/Foundation.h>


@interface OutlinesItem : NSObject
{
    int pageNumber;
    int titleLevel;
    NSString *pageTitle;
}

@property (nonatomic) int pageNumber;
@property (nonatomic) int titleLevel;
@property (nonatomic, retain) NSString *pageTitle;

- (Boolean) setupNode: (CGPDFDictionaryRef) nodeDictionary ofPdfDoc:(CGPDFDocumentRef)pdfDoc level: (int) level previousPage: (int) previousPageNumber toc: (NSMutableArray*)toc;
- (int) setInfo: (CGPDFDictionaryRef) childDictionary ofPdfDoc:(CGPDFDocumentRef)pdfDoc toc: (NSMutableArray*)toc;

@end
