<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Barcode Labels for {{ $data['productName'] }}</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <style>
        #print-area {
            width: 100%;
        }

        .label-container {
            width: 50mm;
            height: 25mm;
            /* padding: 0.8mm 2mm; */
            padding: 1mm 2mm;
            border: 1px dashed #aaa;
            box-sizing: border-box;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            margin: 0 auto 10px auto;
            gap: 0.3mm;
        }

        .label-text {
            font-family: "Arial", "Helvetica", sans-serif;
            font-weight: 600;
            text-align: center;
            width: 100%;
            line-height: 1.2;
            margin: 0;
            padding: 0;
        }

        .text-lg {
            font-size: 7pt;
        }
        .text-xl {
            font-size: 8pt;
        }

        .text-sm {
            font-size: 5pt;
        }

        .text-xs {
            font-size: 4.5pt;
        }

        .fw-bold {
            font-weight: 700;
        }

        .fw-normal {
            font-weight: 500;
        }

        .barcode-svg {
            width: 98%;
            height: auto;
            max-height: 11mm;
            flex-shrink: 0;
        }

        .price-text {
            margin-top: 0.5mm;
        }

        @media print {
            @page {
                size: 50mm 25mm;
                margin: 0;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .label-container {
                border: none;
                margin: 0;
                /* padding: 0.8mm 2mm; */
                padding: 1mm 2mm;
                page-break-after: always;
            }

            #print-area {
                margin: 0;
                padding: 0;
            }

            .label-text {
                font-family: "Arial", "Helvetica", sans-serif;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .fw-bold {
                font-weight: 700;
            }

            .fw-normal {
                font-weight: 500;
            }

            .text-lg {
                font-size: 7pt;
            }

            .text-xl {
                font-size: 8pt;
            }

            .text-sm {
                font-size: 5pt;
            }
        }
    </style>
</head>

<body>

    <div id="print-area"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            printBarcodes();
        });

        function printBarcodes() {
            const sellerName = "{{ $data['sellerName'] }}";
            const productName = "{{ $data['productName'] }}";
            const price = "{{ $data['price'] }}";
            const barcodeData = "{{ $data['sku'] }}";
            const variantName = "{{ $data['variantName'] }}"; // New variant name data
            const labelCount = parseInt("{{ $data['quantity'] }}");
            const printArea = document.getElementById("print-area");

            printArea.innerHTML = "";

            for (let i = 0; i < labelCount; i++) {
                const labelContainer = document.createElement("div");
                labelContainer.className = "label-container";

                const sellerNameElement = document.createElement("div");
                sellerNameElement.className = "label-text fw-bold text-lg";
                sellerNameElement.textContent = sellerName;

                const nameElement = document.createElement("div");
                nameElement.className = "label-text fw-normal text-sm";
                nameElement.textContent = productName;

                const variantElement = document.createElement("div");
                variantElement.className = "label-text fw-normal text-xs";
                if (variantName) {
                    variantElement.textContent = variantName;
                }

                const priceElement = document.createElement("div");
                priceElement.className = "label-text fw-bold text-xl price-text";
                priceElement.textContent = 'Price: ' + price;

                const barcodeElement = document.createElementNS("http://www.w3.org/2000/svg", "svg");
                barcodeElement.className = "barcode-svg";
                barcodeElement.id = `barcode-${i}`;

                labelContainer.appendChild(sellerNameElement);
                labelContainer.appendChild(nameElement);

                if (variantName) {
                    labelContainer.appendChild(variantElement);
                }

                labelContainer.appendChild(barcodeElement);
                labelContainer.appendChild(priceElement);
                printArea.appendChild(labelContainer);

                try {
                    JsBarcode(`#${barcodeElement.id}`, barcodeData, {
                        format: "CODE128",
                        displayValue: true,
                        fontSize: 8,
                        height: 30,
                        margin: 0,
                        width: 1
                    });
                } catch (e) {
                    console.error(e);
                    alert(
                        "Error generating barcode. Check Barcode Data and try again."
                    );
                    printArea.innerHTML = "";
                    return;
                }
            }
            if (labelCount > 0) {
                window.print();
            }
        }
    </script>
</body>

</html>
