<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>

body{
    font-family: Arial, sans-serif;
    font-size:10px;
}

.dtr-container{
    width:100%;
}

.dtr-copy{
    width:48%;
    display:inline-block;
    vertical-align:top;
}

.logo{
    width:40px;
}

.office-title{
    text-align:center;
    font-weight:bold;
    margin-bottom:10px;
}

.info-table{
    width:100%;
}

.dtr-table{
    width:100%;
    border-collapse:collapse;
}

.dtr-table th,
.dtr-table td{
    border:1px solid #000;
    font-size:9px;
    padding:2px;
    text-align:center;
}

.signature{
    margin-top:20px;
    text-align:center;
}

.line{
    border-top:1px solid #000;
    margin-top:20px;
}

</style>
</head>

<body>

<div class="dtr-container">

    @include(
        'livewire.admin.reports.daily-time-record.employee.partials.dtr-copy'
    )

    @include(
        'livewire.admin.reports.daily-time-record.employee.partials.dtr-copy'
    )

</div>

</body>
</html>