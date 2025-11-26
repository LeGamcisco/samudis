<table border="1" style="border: #000">
    <thead>
        <tr>
            <th style="font-weight:bold; font-size:x-large" colspan="13" align="center" padding="5">{{ $config->customer_name ?? '-' }} UNIT <?= @$stack->sispek_code?></th>
        </tr>
        <tr>
            <th style="font-weight:bold; font-size:x-large" colspan="13" align="center" padding="5">PERIODE <?= @$monthYear?></th>
        </tr>
        <tr>
            <td>Company Identity</td>
        </tr>
        <tr>
            <td>· Name</td>
            <td>{{ $config->customer_name ?? '-' }}</td>
        </tr>
        <tr>
            <td rowspan="2">· Address</td>
            <td>{{ $config->address ?? '-' }}</td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td>· Type</td>
            <td></td>
        </tr>
        <tr>
            <td>· Capasity</td>
            <td>-</td>
        </tr>
        <tr style="background: #D9D9D9">
            <th rowspan="4  ">
                Day
            </th>
            <th rowspan="4  ">
                Data / Hour
            </th>
            <th colspan="12" style="text-align: center;font-weight:bold">CONCENTRATION DAILY RATE</th>
        </tr>
        <tr style="background: #D9D9D9; text-align:center; font-weight:bold">
            <th padding="1">O2</th>
            <th padding="1">CO</th>
            <th padding="1">CO2</th>
            <th padding="1" colspan="2">SO2</th>
            <th padding="1" colspan="2">NOX</th>
            <th padding="1" colspan="2">HG</th>
            <th padding="1" colspan="2">DUST</th>
            <th padding="1">Average Flow</th>
        </tr>
        <tr style="background: #D9D9D9; text-align:center">
            <td rowspan="2">%</td>
            <td rowspan="2">(mg/Nm<sup>3</sup>)</td>
            <td rowspan="2">(mg/Nm<sup>3</sup>)</td>
            <td colspan="2">(mg/Nm<sup>3</sup>)</td>
            <td colspan="2">(mg/Nm<sup>3</sup>)</td>
            <td colspan="2">(mg/Nm<sup>3</sup>)</td>
            <td colspan="2">(mg/Nm<sup>3</sup>)</td>
            <td rowspan="2">(Nm<sup>3</sup>/h)</td>
        </tr>
        <tr style="background: #D9D9D9">
            <td>Actual</td>
            <td>Correction</td>
            <td>Actual</td>
            <td>Correction</td>
            <td>Actual</td>
            <td>Correction</td>
            <td>Actual</td>
            <td>Correction</td>
        </tr>
    </thead>
    <tbody style="text-align: center">
        @foreach ($logs as $log)
            <tr>
                <td>{{ $log['day'] }}</td>
                <td>{{ $log['hour'] }}</td>
                <td>{{ $log['o2_value'] }}</td>
                <td>{{ $log['co_value'] }}</td>
                <td>{{ $log['co2_value'] }}</td>
                <td>{{ $log['so2_measured'] }}</td>
                <td>{{ $log['so2_corrective'] }}</td>
                <td>{{ $log['nox_measured'] }}</td>
                <td>{{ $log['nox_corrective'] }}</td>
                <td>{{ $log['hg_measured'] }}</td>
                <td>{{ $log['hg_corrective'] }}</td>
                <td>{{ $log['dust_measured'] }}</td>
                <td>{{ $log['dust_corrective'] }}</td>
                <td>{{ $log['flow_measured'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>