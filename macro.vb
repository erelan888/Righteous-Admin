Function CheckStore(store As String, id As Integer)
    If ActiveCell.Offset(0, 3).Value = store Then
        ActiveCell.Offset(0, 1).Value = "os-" & id & "-" & ActiveCell.Offset(0, 1).Value
    End If
End Function
Sub Ordoro()
'
' Ordoro Macro
'
'
    Dim rngCell As Range
    Application.ScreenUpdating = False
    
    Range("A2").Select
    Do Until IsEmpty(ActiveCell)
        'Remove extra shipments
        If ActiveCell.Offset(0, 48).Value = 0 And IsEmpty(ActiveCell.Offset(0, 40).Value) = False Then
            ActiveCell.EntireRow.Delete
        Else
            'Adjust store order number
 '           Call CheckStore("Tilted Kilt Retail", 3)
 '           Call CheckStore("Buffalo Wild Wings Retail", 4)
            Call CheckStore("Quaker Steak & Lube Retail", 5)
            Call CheckStore("PeaceHealth", 6)
 '           Call CheckStore("Hoptomistic Retail", 7)
            Call CheckStore("Pancheros Retail", 8)
            Call CheckStore("Pancheros Corp", 9)
 '           Call CheckStore("Toppers Pizza Retail", 10)
 '           Call CheckStore("Pizza Factory Corp", 11)
            Call CheckStore("Kum & Go Retail", 12)
            Call CheckStore("Elmer's Corp", 13)
            Call CheckStore("bd's Corp", 14)
 '           Call CheckStore("Toppers Pizza Corp", 15)
            Call CheckStore("Quaker Steak & Lube Corp", 16)
            Call CheckStore("Red Robin Retail", 17)
            Call CheckStore("Egg N' Joe", 18)
            Call CheckStore("Skagit Regional Health", 19)
            Call CheckStore("Shari's Corp", 20)
            Call CheckStore("Flat Top Grill Corp", 21)
            Call CheckStore("Red Robin Corp", 22)
            Call CheckStore("Bennigan's Corp", 23)
            Call CheckStore("Pacific Seafood Online Store", 24)
 '           Call CheckStore("Tilted Kilt Corp", 26)
            Call CheckStore("Houlihan's Corp", 27)
 '           Call CheckStore("Zeppidy", 28)
            Call CheckStore("Bennigan's Retail", 29)
            Call CheckStore("Great Harvest Bread Corp", 30)
            Call CheckStore("Building Champions, Inc.", 31)
            Call CheckStore("Port of Subs", 32)
            Call CheckStore("Kum & Go Corp", 33)
 '           Call CheckStore("NextGen", 34)
            Call CheckStore("Cleansing Stream", 35)
            Call CheckStore("APU", 36)
            Call CheckStore("Foursquare Online Store", 37)
            Call CheckStore("TRS Group", 38)
            Call CheckStore("MSA Coalition Online Retail Store", 39)
 '           Call CheckStore("TEC Corp", 40)
 '           Call CheckStore("Fosters Freeze", 41)
            Call CheckStore("Concordia University", 42)
            Call CheckStore("Furious Spoon Corp", 43)
            Call CheckStore("CC's Coffee House Corp", 44)
            Call CheckStore("Papa Murphy's Online Store", 46)
            Call CheckStore("Dave & Buster's Online Store", 47)
            Call CheckStore("PJW Restaurant Group Online Store", 49)
            Call CheckStore("Genghis Grill Online Store", 50)
            Call CheckStore("Frenchies Nails Online Store", 48)
            Call CheckStore("Walk On's Central Online Store", 52)
            Call CheckStore("Walk On's Retail Online Store", 53)
            Call CheckStore("Life Pacific University Online Store", 54)
            'Correct Kum & Go Rewards cards for retail
            If ActiveCell.Offset(0, 3).Value = "Kum & Go Retail" Then
                ActiveCell.Offset(0, 11).Value = "Credit Card"
            End If
            'Correct Frenchies Missing Credit Card Issues
            If ActiveCell.Offset(0,3).Value = "Frenchies Nails Online Store" Then
                ActiveCell.Offset(0,11).Value = "Credit Card"
            End If
            'Add tax code
            ActiveCell.Offset(0, 57).FormulaR1C1 = "=IF(RC[-47]=0,""NON"",""TAX"")"
            'Calculate product cost
            ActiveCell.Offset(0, 58).FormulaR1C1 = "=RC[-10]/RC[-11]"
            'Truncate Sku Description to prevent upload issues
            Dim desc as String 
            desc = Left(ActiveCell.offset(0,45),30)
            ActiveCell.Offset(0, 45).Value = desc
            'Change Bill-TO field so its not so damn long
            If InStr(ActiveCell.Offset(0,12).Value, "Frenchies") Then
                Dim buyerName as String
                buyerName = "Frenchies Modern Nail Care"
                ActiveCell.Offset(0,12).Value = buyerName
            End If
            'Change Bill-To field so its not so damn long
            If InStr(ActiveCell.Offset(0,12).Value, "Great Harvest") Then
                buyerName = "Great Harvest Bread Co"
                ActiveCell.Offset(0,12).Value = buyerName
            End If
            'Select next line
            ActiveCell.Offset(1, 0).Select
        End If
    Loop
    Range("BG1").FormulaR1C1 = "Product cost"
    Range("BF1").FormulaR1C1 = "Tax code"
    
    ActiveSheet.Name = "Invoices"
    Sheets.Add After:=Sheets(Sheets.Count)
    Sheets("Sheet1").Name = "Sales Receipts"
    Sheets.Add After:=Sheets(Sheets.Count)
    Sheets("Sheet2").Name = "Criteria"
    Range("A1").Value = "Credit Card Issuer"
    Range("A2").Value = "Credit Card"
    
    Sheets("Invoices").Select
    With Excel.ActiveSheet
        Range("A2").Select
        Do Until IsEmpty(ActiveCell)
            If InStr(ActiveCell.Offset(0, 11).Value, " and Account Funds") Then
                ActiveCell.EntireRow.Copy
                ActiveCell.Offset(1).Insert Shift:=xlDown
                ActiveCell.Offset(0, 11).Value = "Partial Account Funds"
                ActiveCell.Offset(0, 53).FormulaR1C1 = "=RC[-45]/RC[-48]" 'this part doesn't make sense
                ActiveCell.Offset(0, 8).Value = "0"
                ActiveCell.Offset(2, 0).Select
            Else
                ActiveCell.Offset(1, 0).Select
            End If
        Loop
    End With
    
    Sheets("Invoices").Range("A:BH").AdvancedFilter _
        Action:=xlFilterCopy, _
        CriteriaRange:=Sheets("Criteria").Range("a1:a2"), _
        CopyToRange:=Sheets("Sales Receipts").Range("A1"), _
        Unique:=False
        
    Sheets("Invoices").Select
    With Excel.ActiveSheet
        Range("A2").Select
        Do Until IsEmpty(ActiveCell)

            If InStr(ActiveCell.Offset(0, 1).Value, "os-13-") Then
                If InStr(ActiveCell.Offset(0, 12).Value, "Elmer's Restaurants, Inc.") Or IsEmpty(ActiveCell.Offset(0, 12).Value) = True Then
                    ActiveCell.Offset(0, 3).Value = "Elmer's Restaurants, Inc."
                End If
            End If
            'Cleanup Cancelled and credit card orders
            If InStr(ActiveCell.Offset(0, 4).Value, "cancelled") Or InStr(ActiveCell.Offset(0, 11).Value, "Credit Card") Then
                ActiveCell.EntireRow.Delete
            'Remove deleted items
            ElseIf InStr(ActiveCell.Offset(0, 39).Value, "deleted") Then
                ActiveCell.EntireRow.Delete
            'Remove Kum and Go Corp Orders
            ElseIf InStr(ActiveCell.Offset(0, 1).Value, "os-33-") Then
                ActiveCell.EntireRow.Delete
            'Remove APU Corp Orders
            ElseIf InStr(ActiveCell.Offset(0, 1).Value, "os-36-") Then
                ActiveCell.EntireRow.Delete
            '6/13/2019 Dustin edit for Dave & Busters Corp Orders
            ElseIf InStr(ActiveCell.Offset(0, 1).Value, "os-47-") Then
                ActiveCell.EntireRow.Delete
            Else
                ActiveCell.Offset(1, 0).Select
           End If

        Loop
    End With
    
    Application.ScreenUpdating = True
    
    Sheets("Invoices").Select
    ActiveWorkbook.SaveAs Filename:="Invoices.csv", _
        FileFormat:=xlCSV, CreateBackup:=False
    Sheets("Sales Receipts").Select
    ActiveWorkbook.SaveAs Filename:="Sales Receipts.csv" _
        , FileFormat:=xlCSV, CreateBackup:=False
        
        
End Sub



