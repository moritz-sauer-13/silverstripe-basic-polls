<% if $ChartData %>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Lade die Chart.js Bibliothek -->
    <canvas id="pollChart" style="max-width: 1000px; max-height: 60vh"></canvas> <!-- Setze die Größe des Canvas -->
    <script>
        var ctx = document.getElementById('pollChart').getContext('2d');
        var chartData = JSON.parse(decodeURIComponent('$ChartData.RAW'));

        // Ersetzen der + Zeichen in den Labels
        let labels = chartData.labels.map(label => label.split('+').join(' '));

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Anzahl der Stimmen',
                    data: chartData.counts,
                    backgroundColor: [
                        'rgba(0, 0, 255, 1)', // blau
                        'rgba(254, 75, 70, 1)', // leuchtrot
                        'rgba(141, 152, 127, 1)', // pistazie
                        'rgba(63, 67, 70, 1)', // anthrazit
                        'rgba(165, 149, 150, 1)', // grau
                        'rgba(255, 255, 255, 1)', // weiss
                    ],
                    borderColor: [
                        'rgba(0, 0, 255, 1)', // blau
                        'rgba(254, 75, 70, 1)', // leuchtrot
                        'rgba(141, 152, 127, 1)', // pistazie
                        'rgba(63, 67, 70, 1)', // anthrazit
                        'rgba(165, 149, 150, 1)', // grau
                        'rgba(255, 255, 255, 1)', // weiss
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1 // Setze den Abstand zwischen den Ticks
                        }
                    },
                    x: {
                        ticks: {
                            stepSize: 1,
                            autoSkip: false,
                            maxRotation: 90,
                        }
                    }
                }
            }
        });
    </script>
<% else %>
    <p>Keine Daten verfügbar.</p>
<% end_if %>