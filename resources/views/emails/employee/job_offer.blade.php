<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Offer Letter</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">

    <p>Hello {{ucwords($data['fullname'])}},</p>

    <p>
        We are pleased to extend an offer for you to join <strong>{{ucwords($data['company_name'])}}</strong> as our new <strong>{{$data['position']}}</strong>. 
        Based on your impressive skills, experience, and interview performance, we are confident that you will make a valuable addition to our team.
    </p>

    <h3>Offer Details:</h3>
    <ul>
        <li><strong>Company</strong>: {{ucwords($data['company_name'])}}</li>
        <li><strong>Position</strong>: {{ucwords($data['position'])}}</li>
        <li><strong>Work Setup</strong>: {{ucwords($data['setup'])}}</li>
        <li><strong>Employment Type</strong>: {{ucwords(str_replace('-', ' ', $data['type']))}}</li>
        <li><strong>Start Date</strong>: {{ucwords($data['starting_date'])}}</li>
        <li><strong>Salary</strong>: {{ucwords($data['salary'])}}</li>
    </ul>

    <p>We’re excited to welcome you to a supportive, growth-oriented environment where you will have the opportunity to make a meaningful impact on our projects and culture. We believe your expertise will be instrumental in achieving our team’s goals.</p>

    <h3>Next Steps:</h3>
    <p>Please review the attached document, which includes the full terms and conditions of the offer. To confirm your acceptance, simply sign the attached offer letter and return it by <strong>uploading it to our website under profile and placement tab</strong>.</p>

    <p>If you have any questions regarding the offer or the details of your employment, feel free to reach out to support@{{env('COMPANY_DOMAIN ')}}.</p>

    <p>Congratulations again, {{ucwords($data['fullname'])}}! We look forward to the opportunity to work together and are excited about the contributions you’ll bring to our team.</p>

    <p>Warm regards,</p>
    <p>HR Department</p>

</body>
</html>
