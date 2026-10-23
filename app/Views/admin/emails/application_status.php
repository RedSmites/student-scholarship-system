<?php
$name = trim((string) ($application['applicant_name'] ?? '')) ?: 'Applicant';
$scholarship = trim((string) ($application['scholarship_title'] ?? 'Scholarship program'));
?>
<!doctype html>
<html lang="en">
<body style="margin:0;padding:0;background-color:#f2f5f9;font-family:Arial,Helvetica,sans-serif;color:#1e293b;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f2f5f9;padding:28px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:620px;background-color:#ffffff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:22px 28px;background-color:#123c63;color:#ffffff;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td width="42" valign="middle">
                                        <div style="width:34px;height:34px;line-height:34px;text-align:center;border-radius:9px;background-color:#ffffff;color:#123c63;font-size:19px;font-weight:bold;">S</div>
                                    </td>
                                    <td valign="middle" style="padding-left:10px;font-size:17px;font-weight:bold;">Scholarship Office</td>
                                    <td align="right" valign="middle" style="font-size:11px;letter-spacing:1px;color:#d7e6f4;">APPLICATION UPDATE</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:30px 28px 12px;">
                            <p style="margin:0 0 8px;font-size:14px;color:#64748b;">Hello <?= esc($name) ?>,</p>
                            <h1 style="margin:0 0 10px;font-size:24px;line-height:1.3;color:#142b45;">Your application has been updated</h1>
                            <p style="margin:0;font-size:14px;line-height:1.7;color:#526174;">Here is the latest update for <strong style="color:#1e293b;"><?= esc($scholarship) ?></strong>.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:<?= esc($statusTint) ?>;border:1px solid <?= esc($statusColor) ?>;border-radius:10px;">
                                <tr>
                                    <td width="56" valign="middle" style="padding:16px 0 16px 18px;">
                                        <div style="width:34px;height:34px;line-height:34px;text-align:center;border-radius:50%;background-color:#ffffff;color:<?= esc($statusColor) ?>;font-size:20px;font-weight:bold;"><?= esc($statusIcon) ?></div>
                                    </td>
                                    <td valign="middle" style="padding:14px 18px 14px 8px;">
                                        <div style="font-size:11px;letter-spacing:1px;font-weight:bold;color:<?= esc($statusColor) ?>;"><?= esc($decisionHeading) ?></div>
                                        <div style="margin-top:4px;font-size:19px;line-height:1.3;font-weight:bold;color:<?= esc($statusColor) ?>;"><?= esc($decision) ?></div>
                                        <div style="margin-top:5px;font-size:13px;line-height:1.5;color:#475569;"><?= esc($headline) ?></div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 28px 18px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                <tr>
                                    <td style="padding:11px 12px;border:1px solid #e2e8f0;background-color:#f8fafc;font-size:12px;color:#64748b;">APPLICATION</td>
                                    <td style="padding:11px 12px;border:1px solid #e2e8f0;font-size:13px;font-weight:bold;color:#1e293b;">#<?= (int) ($application['id'] ?? 0) ?></td>
                                    <td style="padding:11px 12px;border:1px solid #e2e8f0;background-color:#f8fafc;font-size:12px;color:#64748b;">STATUS</td>
                                    <td style="padding:11px 12px;border:1px solid #e2e8f0;font-size:13px;font-weight:bold;color:#1e293b;"><?= esc($statusLabel) ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 28px 20px;">
                            <h2 style="margin:0 0 9px;font-size:15px;color:#173b5e;">Administrator remarks</h2>
                            <div style="padding:14px 15px;border:1px solid #e2e8f0;border-radius:8px;background-color:#f8fafc;font-size:13px;line-height:1.7;color:#334155;"><?= $remarks !== '' ? nl2br(esc($remarks)) : 'No additional remarks were provided.' ?></div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 28px 24px;">
                            <h2 style="margin:0 0 9px;font-size:15px;color:#173b5e;">Requirement review</h2>
                            <?php if ($documents !== []): ?>
                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:collapse;">
                                    <?php foreach ($documents as $document): ?>
                                        <?php $verified = ! empty($document['admin_verified']); ?>
                                        <tr>
                                            <td style="padding:12px;border:1px solid #e2e8f0;">
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                    <tr>
                                                        <td style="font-size:13px;font-weight:bold;color:#1e293b;"><?= esc($document['requirement_name_snapshot'] ?? 'Requirement') ?></td>
                                                        <td align="right" style="font-size:11px;font-weight:bold;color:<?= $verified ? '#147d64' : '#9a5b00' ?>;"><?= $verified ? 'VERIFIED' : 'NOT VERIFIED' ?></td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" style="padding-top:7px;font-size:12px;line-height:1.6;color:#526174;"><?= ! empty($document['admin_remarks']) ? nl2br(esc($document['admin_remarks'])) : 'No comment provided.' ?></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php endforeach ?>
                                </table>
                            <?php else: ?>
                                <div style="padding:13px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:13px;color:#64748b;">No requirement documents have been submitted for review.</div>
                            <?php endif ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 28px;background-color:#f8fafc;border-top:1px solid #e2e8f0;font-size:12px;line-height:1.6;color:#64748b;">
                            If you have questions about this update, reply to this email to contact the Scholarship Office.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
