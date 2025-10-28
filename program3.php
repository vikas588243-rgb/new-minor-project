<!DOCTYPE html>
<html>
<head>
    <title>Matrix Calculator</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .matrix-input { display: flex; gap: 20px; margin-bottom: 20px; }
        .matrix { border: 1px solid #ccc; padding: 10px; }
        .matrix input { width: 40px; text-align: center; margin: 2px; }
        h2 { border-bottom: 2px solid #eee; padding-bottom: 5px; }
        pre { background-color: #f4f4f4; padding: 10px; border: 1px solid #ddd; }
    </style>
</head>
<body>

    <h1>Matrix Operations</h1>

    <form method="post" action="">
        <div class="matrix-input">
            <div class="matrix">
                <h2>Matrix A (2x2)</h2>
                <div>
                    <input type="number" name="A[0][0]" value="1" required>
                    <input type="number" name="A[0][1]" value="2" required>
                </div>
                <div>
                    <input type="number" name="A[1][0]" value="3" required>
                    <input type="number" name="A[1][1]" value="4" required>
                </div>
            </div>

            <div class="matrix">
                <h2>Matrix B (2x2)</h2>
                <div>
                    <input type="number" name="B[0][0]" value="5" required>
                    <input type="number" name="B[0][1]" value="6" required>
                </div>
                <div>
                    <input type="number" name="B[1][0]" value="7" required>
                    <input type="number" name="B[1][1]" value="8" required>
                </div>
            </div>
        </div>

        <label for="operation">Select Operation:</label>
        <select name="operation" id="operation" required>
            <option value="add">Addition (A + B)</option>
            <option value="sub">Subtraction (A - B)</option>
            <option value="mul">Multiplication (A * B)</option>
        </select>
        <button type="submit">Calculate</button>
    </form>

    <hr>

    <?php
    // --- PHP Scripting Section ---

    // **1. Corrected and Implemented PHP Functions**

    /**
     * Corrected PHP functions for matrix operations.
     * Note: Original functions had errors in loop conditions and variable names.
     */

    function checkDimensions($A, $B, $operation) {
        $rowsA = count($A);
        $colsA = count($A[0]);
        $rowsB = count($B);
        $colsB = count($B[0]);

        if ($operation === 'add' || $operation === 'sub') {
            if ($rowsA !== $rowsB || $colsA !== $colsB) {
                return "Error: For addition/subtraction, matrices must have the same dimensions.";
            }
        } elseif ($operation === 'mul') {
            if ($colsA !== $rowsB) {
                return "Error: For multiplication, columns of A must match rows of B ($colsA != $rowsB).";
            }
        }
        return true;
    }

    function addMatrix($A, $B) {
        $rows = count($A);
        $cols = count($A[0]);
        $result = [];
        // Corrected loop condition: $i < $rows
        for ($i = 0; $i < $rows; $i++) {
            $result[$i] = [];
            for ($j = 0; $j < $cols; $j++) {
                // Corrected array access: $A[$i][$j], $B[$i][$j]
                $result[$i][$j] = $A[$i][$j] + $B[$i][$j];
            }
        }
        return $result;
    }

    function subMatrix($A, $B) {
        $rows = count($A);
        $cols = count($A[0]);
        $result = [];
        // Corrected loop condition: $i < $rows
        for ($i = 0; $i < $rows; $i++) {
            $result[$i] = [];
            for ($j = 0; $j < $cols; $j++) {
                // Corrected array access: $A[$i][$j], $B[$i][$j]
                $result[$i][$j] = $A[$i][$j] - $B[$i][$j];
            }
        }
        return $result;
    }

    function mulMatrix($A, $B) {
        $rowsA = count($A);
        $colsA = count($A[0]);
        $colsB = count($B[0]); // Result columns

        $result = [];
        // Loop through rows of A (i)
        for ($i = 0; $i < $rowsA; $i++) { // Corrected loop condition: $i < $rowsA
            $result[$i] = [];
            // Loop through columns of B (j)
            for ($j = 0; $j < $colsB; $j++) { // Corrected loop condition: $j < $colsB
                $result[$i][$j] = 0;
                // Loop for the dot product (k)
                for ($k = 0; $k < $colsA; $k++) {
                    // Corrected formula for matrix multiplication: A[i][k] * B[k][j]
                    $result[$i][$j] += $A[$i][$k] * $B[$k][$j];
                }
            }
        }
        return $result;
    }

    function printMatrix($M) {
        if (is_array($M) && !empty($M)) {
            echo "<pre>";
            foreach ($M as $row) {
                echo implode("  ", $row) . "\n";
            }
            echo "</pre>";
        }
    }


    // **2. Form Processing Logic**

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get matrices and operation from POST data
        $matrixA = $_POST['A'] ?? [];
        $matrixB = $_POST['B'] ?? [];
        $operation = $_POST['operation'] ?? '';

        // Type-cast input strings to integers/floats for calculation
        $A = array_map(function($row) {
            return array_map('floatval', $row);
        }, $matrixA);

        $B = array_map(function($row) {
            return array_map('floatval', $row);
        }, $matrixB);

        $error = checkDimensions($A, $B, $operation);

        if ($error !== true) {
            echo "<h2>Calculation Error</h2>";
            echo "<p style='color:red;'>$error</p>";
        } else {
            $result = [];
            $operationSymbol = '';
            $functionToCall = '';

            switch ($operation) {
                case 'add':
                    $functionToCall = 'addMatrix';
                    $operationSymbol = '+';
                    break;
                case 'sub':
                    $functionToCall = 'subMatrix';
                    $operationSymbol = '-';
                    break;
                case 'mul':
                    $functionToCall = 'mulMatrix';
                    $operationSymbol = '×';
                    break;
                default:
                    echo "<p>Please select a valid operation.</p>";
                    goto end_of_script; // Skip calculation if no operation
            }

            // Perform the calculation
            $result = $functionToCall($A, $B);

            // Display Results
            echo "<h2>Result: (A $operationSymbol B)</h2>";
            echo "<h3>Matrix A:</h3>";
            printMatrix($A);
            echo "<h3>Matrix B:</h3>";
            printMatrix($B);
            echo "<h3>Result Matrix:</h3>";
            printMatrix($result);
        }
    }

    end_of_script:
    ?>

</body>
</html>