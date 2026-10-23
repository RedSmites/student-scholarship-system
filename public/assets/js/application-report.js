const printReportButton = document.getElementById('print-report');

if (printReportButton) {
    printReportButton.addEventListener('click', () => window.print());
}
