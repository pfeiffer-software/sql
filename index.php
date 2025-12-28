<?php

/* Dieses Programm wurde von Michèle René Sälzle (msaelzle88@gmail.com) für den Informatikunterricht programmiert.
 * Dieses Programm darf kostenfrei weiter verbreitet werden, sofern dieser Hinweis bestehen bleibt. Auch eine Änderung 
 * des Programmcodes ist gestattet. 
 */

// Config:
$separator = ";";
// End config

// GET-Parameter prüfen
if (!isset($_GET['table'])) {
    die("Fehler: Kein Parameter 'table' übergeben.");
}

$tid = $_GET['table'];

// Prüfen, ob $tid numerisch ist
if (!is_numeric($tid)) {
    die("Fehler: Der Parameter 'table' muss eine Zahl sein.");
}

// Dateinamen bestimmen
$fileLeft = $tid . "_l.csv";
$fileRight = $tid . "_r.csv";

// Alternative Dateinamen mit .CSV
$fileLeftUpper = $tid . "_l.CSV";
$fileRightUpper = $tid . "_r.CSV";

if(!file_exists($fileLeft) && file_exists($fileLeftUpper)){
	$fileLeft = $fileLeftUpper;
}
if(!file_exists($fileRight) && file_exists($fileRightUpper)){
	$fileRight = $fileRightUpper;
}

// Prüfen, ob beide Dateien existieren
if (!file_exists($fileLeft) || !file_exists($fileRight)) {
    die("Fehler: Eine oder beide CSV-Dateien sind nicht vorhanden.");
}

// CSV-Dateien einlesen
function readCsvFile($filename) {
    global $separator;
    $rows = [];
    if (($handle = fopen($filename, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 0, $separator)) !== FALSE) {
            $rows[] = $data;
        }
        fclose($handle);
    }
    return $rows;
}

$leftData = readCsvFile($fileLeft);
$rightData = readCsvFile($fileRight);
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<title>JOIN-Operator</title>
<style>
    body {
        font-family: Arial, sans-serif;
    }
    .table-container {
        display: flex;
        align-items: stretch; /* Stellt sicher, dass beide Tabellen und der Mittelteil gleich hoch sind */
        margin: 20px;
    }
    .table-left-container, .table-right-container {
        flex: 0 0 auto; /* nimmt nur so viel Platz wie nötig */
    }
    .middle-container {
        flex: 1;
	position: relative; /* ermöglicht absolutes Positionieren der inneren Boxen */
	min-width: 90px;
	min-height: 200px;
    }
    table {
        border-collapse: collapse;
        margin: 10px;
    }
    table, th, td {
        border: 1px solid #000;
        padding: 5px;
    }
<?php
ob_start(); /* Welche Zelle der resultierenden Tabelle fett sein muss (als Trennlinie zwischen den Tabellen) 
               klärt sich erst später. */  
?>
    .table-center {
        margin: 20px auto; 
    }
    .clear {
        clear: both;
    }
    /* Innere Boxen im Mittel-Container */
    .middle-left-box, .middle-right-box {
        position: absolute;
        top: 35%;    /* Start in etwa in der Mitte des Containers */
        bottom: 35px;   /* Bis zum unteren Rand des Containers */
        box-sizing: border-box;
    }
    .middle-left-box {
        left: 0;
        width: 50%;
        border-top: 20px solid #fd0d7a;
        border-right: 20px solid #fd0d7a;
	border-top-right-radius: 50px;    /* Ecken abrunden */
    }
    .middle-right-box {
        right: 0;
        width: 50%;
        border-top: 20px solid #87CEEB;   /* Himmelblau oben */
        border-left: 20px solid #87CEEB;  /* Himmelblau links */
	border-top-left-radius: 50px;     /* Ecken abrunden */
    }
    .middle-left-box-bottom, .middle-right-box-bottom {
	position: absolute;
	top: calc(100% - 35px);
	bottom: 0;
	box-sizing: border-box;
	width: 45px;
    }
    .middle-left-box-bottom {
	right: 50%;
	background-color: #fd0d7a;
	clip-path: polygon(0 0, 100% 100%, 100% 0);
    }
    .middle-right-box-bottom {
        left: 50%;
	background-color: #87CEEB;
	clip-path: polygon(0 0, 0 100%, 100% 0);
    }
    .highlight {
        background-color: yellow !important;
    }
    .used {
        background-color: #d3d3d3;
    }
</div>
</style>
<script>
    // JavaScript für das Highlighting der Tabellenzeilen und das Übernehmen der Daten
    document.addEventListener('DOMContentLoaded', function() {
        // Variable, um die aktuell hervorgehobene Zeile zu speichern
	let highlightedRow = null;

        // Funktion zum Entfernen der Hervorhebung
        function removeHighlight() {
            if (highlightedRow) {
                highlightedRow.classList.remove('highlight');
                highlightedRow = null;
            }
        }

        // Funktion zum Hinzufügen der Hervorhebung
	function addHighlight(row) {
	    if(highlightedRow==row){
	    	removeHighlight();
	    }else{
            	removeHighlight();
            	row.classList.add('highlight');
	    	highlightedRow = row;
	    }
        }

        // Auswahl aller Tabellenzeilen in den linken und rechten Tabellen
        const leftRows = document.querySelectorAll('.table-left-container table tr');
        const rightRows = document.querySelectorAll('.table-right-container table tr');

        // Funktion zur Einrichtung der Hervorhebung für gegebene Zeilen
        function setupHighlighting(rows) {
            rows.forEach(function(row) {
                // Überspringen der Kopfzeilen
                if (row.querySelectorAll('th').length > 0) {
                    return;
                }

                row.addEventListener('click', function() {
                    addHighlight(this);
                });
            });
        }

        setupHighlighting(leftRows);
        setupHighlighting(rightRows);

        // Auswahl der dritten Tabelle (center table)
        const centerTable = document.querySelector('.table-center');
        if (centerTable) {
            centerTable.addEventListener('click', function(event) {
                const clickedRow = event.target.closest('tr');
                if (!clickedRow) return; // Klick außerhalb der Zeilen

                // Überprüfen, ob die Zeile leer ist (alle Zellen sind leer oder enthalten 'NULL' oder &nbsp;)
                const isEmpty = Array.from(clickedRow.querySelectorAll('td')).every(td => {
                    const text = td.textContent.trim();
                    return text === '' || text.toUpperCase() === 'NULL' || text === '\u00A0'; // \u00A0 ist &nbsp;
                });

                // Bestimmen der Anzahl der Header in linken und rechten Tabellen
                const leftHeaderCount = document.querySelectorAll('.table-left-container table th').length;
                const rightHeaderCount = document.querySelectorAll('.table-right-container table th').length;
                const totalHeaders = leftHeaderCount + rightHeaderCount;

                // Funktion zum Einfügen der Daten in die angeklickte Zeile
                function insertDataIntoRow(row, data, sourceTable, isEmptyRow) {
                    const cells = row.querySelectorAll('td');

		    if (sourceTable === 'left') {
                        // Füllen der ersten leftHeaderCount Zellen
                        for (let i = 0; i < leftHeaderCount; i++) {
                            const cellData = data[i];
                            if (cellData !== undefined && cellData.trim() !== '') {
                                cells[i].innerHTML = cellData;
                            } else {
                                if (isEmptyRow) {
                                    cells[i].innerHTML = '<em>NULL</em>';
                                }
                                // Beim Überschreiben bleiben bestehende Inhalte unverändert
                            }
                        }

                        if (isEmptyRow) {
                            // Füllen der restlichen Zellen mit NULL
                            for (let i = leftHeaderCount; i < cells.length; i++) {
                                cells[i].innerHTML = '<em>NULL</em>';
                            }
                        }
                    } else if (sourceTable === 'right') {
                        // Füllen der nachfolgenden rightHeaderCount Zellen
                        for (let i = 0; i < rightHeaderCount; i++) {
                            const cellData = data[i];
                            if (cellData !== undefined && cellData.trim() !== '') {
                                cells[leftHeaderCount + i].innerHTML = cellData;
                            } else {
                                if (isEmptyRow) {
                                    cells[leftHeaderCount + i].innerHTML = '<em>NULL</em>';
                                }
                                // Beim Überschreiben bleiben bestehende Inhalte unverändert
                            }
                        }

                        if (isEmptyRow) {
                            // Füllen der ersten leftHeaderCount Zellen mit NULL
                            for (let i = 0; i < leftHeaderCount; i++) {
                                cells[i].innerHTML = '<em>NULL</em>';
                            }
                        }
                    }
        	    removeHighlight();
                }

                // Funktion zum Finden der Originalzeile in der linken oder rechten Tabelle
                function findOriginalRow(data, sourceTable) {
                    let originalRow = null;
                    if (sourceTable === 'left') {
                        // Suchen in der linken Tabelle anhand der ersten N Spalten
                        leftRows.forEach(function(row) {
                            if (row.querySelectorAll('th').length > 0) return; // Kopfzeile überspringen
                            const rowData = Array.from(row.querySelectorAll('td')).map(td => td.innerHTML.trim());
                            const match = data.slice(0, rowData.length).every((cell, index) => {
                                return cell === rowData[index];
                            });
                            if (match) {
                                originalRow = row;
                            }
                        });
                    } else if (sourceTable === 'right') {
                        // Suchen in der rechten Tabelle anhand der letzten M Spalten
                        rightRows.forEach(function(row) {
                            if (row.querySelectorAll('th').length > 0) return; // Kopfzeile überspringen
                            const rowData = Array.from(row.querySelectorAll('td')).map(td => td.innerHTML.trim());
                            const dataToCompare = data.slice(-rowData.length);
                            const match = dataToCompare.every((cell, index) => {
                                return cell === rowData[index];
                            });
                            if (match) {
                                originalRow = row;
                            }
                        });
                    }
                    return originalRow;
                }

                // Funktion zum Überprüfen, ob die Daten in der dritten Tabelle noch vorhanden sind
		function isDataStillUsed(data, sourceTable) {
                    const rows = centerTable.querySelectorAll('tr');
                    for (let i = 1; i < rows.length; i++) { // Start at 1 to skip header
                        const row = rows[i];
                        const cells = Array.from(row.querySelectorAll('td')).map(td => td.innerHTML.trim());
                        if (sourceTable === 'left') {
                            const rowData = cells.slice(0, leftHeaderCount);
                            const match = data.slice(0, leftHeaderCount).every((cell, index) => cell === rowData[index]);
                            if (match) return true;
                        } else if (sourceTable === 'right') {
                            const rowData = cells.slice(leftHeaderCount);
                            const match = data.every((cell, index) => cell === rowData[index]);
                            if (match) return true;
                        }
                    }
                    return false;
		}
		// Funktion, die alle Rows prüfen soll, ob sie noch benutzt werden.
		function tableClass(elem){
                    // Überprüfen, ob das Element existiert
                    if (!elem) {
                        return "error";
                    }
                
                    // Überprüfen, ob das Element vom Typ "body" ist
                    if (elem.nodeName === "BODY") {
                        return "error";
                    }
                
                    // Überprüfen, ob das Element vom Typ "table" ist
                    if (elem.nodeName === "TABLE") {
                        if (elem.classList.contains("table-left")) {
                            return "left";
                        } else if (elem.classList.contains("table-right")) {
                            return "right";
                        } else {
                            // Rekursiver Aufruf mit dem Elternelement
                            return tableClass(elem.parentElement);
                        }
                    } else {
                        // Wenn das Element kein "table" ist, rekursiver Aufruf mit dem Elternelement
                        return tableClass(elem.parentElement);
                    }
		}
		function prooveAllUsedRows(){
		    const usedElements = document.querySelectorAll('.used');
		    
		    // Iteriere über jedes gefundene Element
		    usedElements.forEach(function(element) {
    		        // Führe gewünschte Aktionen mit jedem 'used'-Element aus
			let sourceTable = tableClass(element);
			if(sourceTable == "error"){
			    return;
			}
                        const cells = element.querySelectorAll('td');
			const data = Array.from(cells).map(cell => cell.innerHTML.trim());
			if(!isDataStillUsed(data,sourceTable)){
			    element.classList.remove('used');
			}
		    });
		}
                // Funktion zum Einfügen der Daten und Markieren der Originalzeile
                function handleCopying(data, sourceTable, rowElement, isEmpty) {
                    // Füllen der angeklickten Zeile
                    insertDataIntoRow(rowElement, data, sourceTable, isEmpty);

                    // Finden und Markieren der Originalzeile
                    const originalRow = findOriginalRow(data, sourceTable);
                    if (originalRow) {
                        // Nur hinzufügen der 'used' Klasse, wenn nicht bereits vorhanden
                        if (!originalRow.classList.contains('used')) {
                            originalRow.classList.add('used');
                        }
                    }
		    if(isEmpty){
                        // Fügen Sie eine neue leere Zeile hinzu, gefüllt mit &nbsp;
                        const newRow = document.createElement('tr');
                        for (let i = 0; i < totalHeaders; i++) {
                            const td = document.createElement('td');
                            td.innerHTML = '&nbsp;'; // Füllen mit &nbsp;
                            newRow.appendChild(td);
                    	}
		    	centerTable.appendChild(newRow);
		    }else{
			// Überprüfe, ob die alten Daten noch genutzt werden.
			prooveAllUsedRows();
                        //const cells = clickedRow.querySelectorAll('td');
                        //const data = Array.from(cells).map(cell => cell.innerHTML.trim());
			
		    }
                }

                if (highlightedRow) {
                    const cells = highlightedRow.querySelectorAll('td');
                    const data = Array.from(cells).map(cell => cell.innerHTML.trim());

                    // Bestimmen der Quelle der hervorgehobenen Zeile
                    let sourceTable = null;
                    if (highlightedRow.closest('.table-left-container')) {
                        sourceTable = 'left';
                    } else if (highlightedRow.closest('.table-right-container')) {
                        sourceTable = 'right';
                    }

                    if (!sourceTable) {
                        return; // Hervorgehobene Zeile ist nicht aus der erwarteten Tabelle
                    }
                    handleCopying(data, sourceTable, clickedRow, isEmpty);
                } else {
                    // Kein Highlight vorhanden, löschen der angeklickten Zeile, wenn sie nicht leer ist
                    if (!isEmpty) {
                        // Speichern der Daten der zu löschenden Zeile
                        const cells = clickedRow.querySelectorAll('td');
                        const data = Array.from(cells).map(cell => cell.innerHTML.trim());

                        // Bestimmen der Quelle basierend auf befüllte Spalten
                        let sourceTable = null;
                        // Check if data in first leftHeaderCount columns are filled
                        const leftData = data.slice(0, leftHeaderCount);
                        const isFromLeft = leftData.some(cell => cell !== '<em>NULL</em>' && cell !== '&nbsp;');
                        if (isFromLeft) {
                            sourceTable = 'left';
                        }

                        // Check if data in last rightHeaderCount columns are filled
                        const rightData = data.slice(leftHeaderCount);
                        const isFromRight = rightData.some(cell => cell !== '<em>NULL</em>' && cell !== '&nbsp;');
                        if (isFromRight) {
                            sourceTable = 'right';
                        }

						if (sourceTable) {
							// Bestimmen der relevanten Daten basierend auf der Quelle
							const relevantData = sourceTable === 'left' ? data.slice(0, leftHeaderCount) : data.slice(leftHeaderCount);

							// Löschen der Zeile
							clickedRow.remove();
							prooveAllUsedRows();
						}
					}
                }
            });
        }
    });
</script>

</head>
<body>

<div class="table-container">
    <div class="table-left-container">
    <?php
    // Erste Tabelle (links)
    echo "<table class='table-left'>";
    if (!empty($leftData)) {
        foreach ($leftData as $index => $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                if ($index === 0) {
                    echo "<th>" . htmlspecialchars($cell) . "</th>";
		} else {
	            if (empty($cell)) {
                        echo "<td><em>NULL</em></td>";
                    } else {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                }
            }
            echo "</tr>";
        }
    }
    echo "</table>";
    ?>
    </div>

    <div class="middle-container">
	<div class="middle-left-box"></div>
        <div class="middle-right-box"></div>
	<div class="middle-left-box-bottom"></div>
        <div class="middle-right-box-bottom"></div>
    </div>

    <div class="table-right-container">
    <?php
    // Zweite Tabelle (rechts)
    echo "<table class='table-right'>";
    if (!empty($rightData)) {
        foreach ($rightData as $index => $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                if ($index === 0) {
                    echo "<th>" . htmlspecialchars($cell) . "</th>";
                } else {
	            if (empty($cell)) {
                        echo "<td><em>NULL</em></td>";
                    } else {
                        echo "<td>" . htmlspecialchars($cell) . "</td>";
                    }
                }
            }
            echo "</tr>";
        }
    }
    echo "</table>";
    ?>
    </div>
</div>

<?php
// Dritte Tabelle: Spalten von Tabelle 1 und 2 zusammenfassen (nur Kopfzeile)
$headersLeft = !empty($leftData) ? $leftData[0] : [];
$headersRight = !empty($rightData) ? $rightData[0] : [];
$combinedHeaders = array_merge($headersLeft, $headersRight);
$content = ob_get_contents();
ob_clean();
echo '    .table-center th:nth-child('.count($headersLeft).'),
    .table-center td:nth-child('.count($headersLeft).') {
        border-right: solid 2px black;
    }
';
echo $content;

echo "<table class='table-center'>";
echo "<tr>";
foreach ($combinedHeaders as $header) {
    echo "<th>" . htmlspecialchars($header) . "</th>";
}
echo "</tr>";
echo "<tr>";
foreach ($combinedHeaders as $header) {
    echo "<td>&nbsp;</td>";
}
echo "</tr>";
echo "</table>";
?>
</body>
</html>

