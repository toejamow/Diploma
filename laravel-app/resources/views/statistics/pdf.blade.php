<!DOCTYPE html>
<html lang="ru">
<head>
  <meta charset="UTF-8">
  <title>Статистика</title>
  <style>
    body {
      font-family: 'DejaVu Sans', sans-serif;
      font-size: 14px;
      color: #111;
    }

    h1, h2, h3 {
      text-align: center;
      margin-bottom: 10px;
    }

    .section {
      margin-top: 15px;
      page-break-inside: avoid;
    }

    .chart {
      text-align: center;
      margin-top: 20px;
    }

    .summary-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .summary-table th,
    .summary-table td {
      border: 1px solid #ccc;
      padding: 8px 12px;
      text-align: left;
    }

    .summary-table th {
      background-color: #f4f4f4;
    }

    .footer {
      margin-top: 10px;
      text-align: center;
      font-size: 12px;
      color: #666;
    }
  </style>
</head>
<body>

  <h1>Отчет по заметкам пользователя {{ $name }}</h1>
    <div class="footer">
    Документ сформирован: {{ $date }}
  </div>

  <div class="section">
    <h3>Общая статистика</h3>
    <table class="summary-table">
      <tr>
        <th>Всего заметок</th>
        <td>{{ $note_count }}</td>
      </tr>
      <tr>
        <th>Всего задач</th>
        <td>{{ $goal_count }}</td>
      </tr>
      <tr>
        <th>Задач выполнено</th>
        <td>{{ $completed_goals }}</td>
      </tr>
      <tr>
        <th>Процент выполнения задач</th>
        <td>{{ $completion_percentage }}%</td>
      </tr>
      <tr>
        <th>Самая старая заметка</th>
        <td>{{ $oldest_note_date }}</td>
      </tr>
      <tr>
        <th>Самая новая заметка</th>
        <td>{{ $newest_note_date }}</td>
      </tr>
    </table>
  </div>

  @if ($taskChartUrl)
    <div class="section">
      <h3>Статистика по выполнению задач</h3>
      <div class="chart">
        <img src="{{ $taskChartUrl }}" alt="Диаграмма задач" width="500">
      </div>
    </div>
  @endif

  @if ($statusChartUrl)
    <div class="section" style="page-break-before: always;">
      <h3>Статистика по статусам заметок</h3>
      <div class="chart">
        <img src="{{ $statusChartUrl }}" alt="Диаграмма статусов" width="500">
      </div>
    </div>
  @endif



</body>
</html>
