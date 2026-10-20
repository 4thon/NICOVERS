<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="theme-color" content="#f6f1ea">
  <title>Sun Son Solar | Portal</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/portal.css') ?>">
</head>
<body>
  <header class="site-nav">
    <a class="brand" href="<?= site_url('/') ?>" aria-label="Sun Son Solar home"><span class="brand-mark">&#9728;</span><span>Sun Son Solar</span></a>
    <div class="nav-actions"><span class="role-chip"><?= esc(ucfirst($role)) ?> portal</span><form method="post" action="<?= site_url('logout') ?>"><input type="hidden" name="csrfToken" value="<?= esc($logoutToken) ?>"><button type="submit">Sign out</button></form></div>
  </header>

  <main class="portal-page role-<?= esc($role) ?>">
    <section class="portal-intro">
      <p class="eyebrow">Secure account</p>
      <h1>Hello, <?= esc($name) ?>.</h1>
      <p><?= esc($summary['focusText']) ?></p>
    </section>

    <section class="stats-grid" aria-label="Account summary">
      <article class="stat-card"><p><?= esc($summary['primaryLabel']) ?></p><strong><?= esc((string) $summary['primaryValue']) ?></strong><span>Current account total</span></article>
      <article class="stat-card"><p><?= esc($summary['secondaryLabel']) ?></p><strong><?= esc((string) $summary['secondaryValue']) ?></strong><span>Recorded in your portal</span></article>
      <article class="stat-card accent"><p>Account role</p><strong><?= esc(ucfirst($role)) ?></strong><span>Access is set by Sun Son Solar</span></article>
    </section>

    <?php if (session('portal_error')): ?><p class="portal-alert is-error" role="alert"><?= esc(session('portal_error')) ?></p><?php endif; ?>
    <?php if (session('portal_success')): ?><p class="portal-alert is-success" role="status"><?= esc(session('portal_success')) ?></p><?php endif; ?>

    <section class="focus-card" id="operations">
      <div><p class="eyebrow">Your next space</p><h2><?= esc($summary['focusTitle']) ?></h2><p><?= esc($summary['focusText']) ?></p></div>
      <a href="<?= esc($summary['actionUrl']) ?>"><?= esc($summary['actionLabel']) ?> <span aria-hidden="true">&#8594;</span></a>
    </section>

    <?php if ($role === 'customer'): ?>
      <section class="workspace-heading"><div><p class="eyebrow">Customer workspace</p><h2>Keep every part of your solar service in view.</h2></div><p>Designed around the steps that matter after you request a consultation, installation, or maintenance visit.</p></section>
      <section class="module-grid customer-modules">
        <a class="module-card module-schedule" href="#customer-schedule"><span class="module-number">01</span><div><p>My schedule</p><h3>Upcoming services</h3><span>Consultation, installation, and maintenance updates.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#customer-updates"><span class="module-number">02</span><div><p>Updates</p><h3>Notifications</h3><span>See reminders, schedule changes, and announcements.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#customer-history"><span class="module-number">03</span><div><p>Records</p><h3>Service history</h3><span>Review completed visits, invoices, and receipts.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#customer-support"><span class="module-number">04</span><div><p>Support</p><h3>Get help</h3><span>Reach Sun Son Solar about your account or service.</span></div><b aria-hidden="true">&#8594;</b></a>
      </section>
      <section class="information-strip" id="customer-schedule"><div><p class="eyebrow">Service visibility</p><h2>Your schedule, always easy to find.</h2></div><p>When a service is confirmed, the date, status, and assigned team update inside this account.</p></section>
    <?php elseif ($role === 'employee'): ?>
      <section class="workspace-heading"><div><p class="eyebrow">Employee workspace</p><h2>Clear tools for the work in front of you.</h2></div><p>Your workspace is limited to assigned jobs and company updates, keeping customer information focused and protected.</p></section>
      <section class="module-grid employee-modules">
        <a class="module-card module-checkin" href="#field-attendance"><span class="module-number">01</span><div><p>Field attendance</p><h3>Check in on site</h3><span>Record your arrival before beginning an assigned visit.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#assigned-work"><span class="module-number">02</span><div><p>My work</p><h3>Assigned jobs</h3><span>View only the customer name, address, and service type you need.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#announcements"><span class="module-number">03</span><div><p>Company updates</p><h3>Announcements</h3><span>Read safety, HR, and operations notices in one place.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#field-report"><span class="module-number">04</span><div><p>Service report</p><h3>Report an issue</h3><span>Send equipment, safety, or customer concerns to the office.</span></div><b aria-hidden="true">&#8594;</b></a>
      </section>
      <section class="analytics-section employee-analytics" id="department-report"><div class="data-section-heading"><div><p class="eyebrow"><?= esc($department) ?> activity</p><h2>Your live department summary.</h2></div><span class="section-count">Private to your sign-in</span></div><div class="analytics-grid"><article><span>Assigned active jobs</span><strong><?= esc((string) $departmentMetrics['assignedJobs']) ?></strong></article><article><span>Reports submitted</span><strong><?= esc((string) $departmentMetrics['submittedReports']) ?></strong></article><article><span>Active teammates</span><strong><?= esc((string) $departmentMetrics['departmentMembers']) ?></strong></article><article><span>Channel updates</span><strong><?= esc((string) $departmentMetrics['departmentMessages']) ?></strong></article></div></section>
      <section class="data-section" id="assigned-work"><div class="data-section-heading"><div><p class="eyebrow">Assigned work</p><h2>Only the job details you need.</h2></div><span class="section-count"><?= count($assignedJobs) ?> active</span></div><?php if ($assignedJobs === []): ?><p class="empty-data">No active service is assigned to your account.</p><?php else: ?><div class="assigned-job-list"><?php foreach ($assignedJobs as $job): ?><article><div><strong><?= esc($job['category_name']) ?></strong><span><?= esc($job['customer_name']) ?> &middot; <?= esc($job['address']) ?></span></div><span class="status-chip status-<?= esc($job['status']) ?>"><?= esc(ucwords(str_replace('_', ' ', $job['status']))) ?></span></article><?php endforeach; ?></div><?php endif; ?></section>
      <section class="employee-action-grid"><form class="compact-action-form" id="field-attendance" method="post" action="<?= site_url('operations/check-in') ?>"><input type="hidden" name="csrfToken" value="<?= esc($dashboardToken) ?>"><p class="eyebrow">Field attendance</p><h2>Check in at a job site.</h2><p>Use your device location only after arriving at an assigned service address.</p><label>Assigned service<select name="serviceRequestId" required><option value="">Choose a service</option><?php foreach ($assignedJobs as $job): ?><option value="<?= esc((string) $job['id']) ?>"><?= esc($job['category_name'] . ' - ' . $job['customer_name']) ?></option><?php endforeach; ?></select></label><div class="field-grid"><label>Latitude<input name="latitude" inputmode="decimal" required></label><label>Longitude<input name="longitude" inputmode="decimal" required></label></div><input name="accuracy" type="hidden"><button type="button" class="location-button" data-location-fill>Use current location</button><button type="submit">Record check-in</button></form><form class="compact-action-form" id="field-report" method="post" action="<?= site_url('operations/report') ?>"><input type="hidden" name="csrfToken" value="<?= esc($dashboardToken) ?>"><p class="eyebrow">Field report</p><h2>Send a service update.</h2><p>Office teams receive the issue, safety, completion, or customer concern report in the system.</p><div class="field-grid"><label>Assigned service<select name="serviceRequestId" required><option value="">Choose a service</option><?php foreach ($assignedJobs as $job): ?><option value="<?= esc((string) $job['id']) ?>"><?= esc($job['category_name'] . ' - ' . $job['customer_name']) ?></option><?php endforeach; ?></select></label><label>Report type<select name="reportType" required><option value="completion">Completion</option><option value="issue">Equipment issue</option><option value="customer_concern">Customer concern</option><option value="safety">Safety</option></select></label></div><label>What happened?<textarea name="summary" maxlength="4000" placeholder="Record the relevant service update..." required><?= esc(old('summary')) ?></textarea></label><button type="submit">Submit report</button></form></section>
    <?php else: ?>
      <section class="workspace-heading"><div><p class="eyebrow">Administration workspace</p><h2>Keep people, schedules, and service operations connected.</h2></div><p>Use this workspace to manage account access and keep customer service work moving with the right information in one place.</p></section>
      <section class="module-grid admin-modules">
        <a class="module-card module-admin" href="#staff-account"><span class="module-number">01</span><div><p>Access control</p><h3>Employee accounts</h3><span>Create, activate, update, or deactivate staff sign in.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#operations"><span class="module-number">02</span><div><p>Service operations</p><h3>Schedule overview</h3><span>Review incoming requests and staff assignments.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#admin-reports"><span class="module-number">03</span><div><p>Business view</p><h3>Reports and activity</h3><span>Track customer activity, service usage, and revenue records.</span></div><b aria-hidden="true">&#8594;</b></a>
        <a class="module-card" href="#admin-content"><span class="module-number">04</span><div><p>Communication</p><h3>Customer updates</h3><span>Plan reminders, promotions, and company notices.</span></div><b aria-hidden="true">&#8594;</b></a>
      </section>
      <section class="information-strip" id="admin-reports"><div><p class="eyebrow">Operations at a glance</p><h2>One source of truth for service work.</h2></div><p>Account access, schedules, customer requests, support, and records are organized for the people responsible for daily operations.</p></section>
    <?php endif; ?>

    <?php if ($role === 'customer'): ?>
      <section class="data-section" id="customer-inquiries">
        <div class="data-section-heading"><div><p class="eyebrow">My product inquiries</p><h2>Product conversations in one place.</h2></div><a href="<?= site_url('products') ?>">Browse catalogue &#8594;</a></div>
        <?php if ($customerInquiries === []): ?><p class="empty-data">You have not submitted a product inquiry yet.</p><?php else: ?><div class="data-list"><?php foreach ($customerInquiries as $inquiry): ?><article class="data-row"><div><strong><?= esc($inquiry['product_name']) ?></strong><span>Sent <?= esc(date('M j, Y', strtotime($inquiry['created_at']))) ?></span></div><span class="status-chip status-<?= esc($inquiry['status']) ?>"><?= esc(ucfirst($inquiry['status'])) ?></span></article><?php endforeach; ?></div><?php endif; ?>
      </section>
    <?php endif; ?>

    <?php if ($role === 'employee' && $department === 'Marketing Sales'): ?>
      <section class="data-section" id="department-inquiries">
        <div class="data-section-heading"><div><p class="eyebrow">Marketing Sales queue</p><h2>New product inquiries for your team.</h2></div><span class="section-count"><?= count($departmentInquiries) ?> inquiry<?= count($departmentInquiries) === 1 ? '' : 'ies' ?></span></div>
        <?php if ($departmentInquiries === []): ?><p class="empty-data">No product inquiry is currently assigned to Marketing Sales.</p><?php else: ?><div class="inquiry-management"><?php foreach ($departmentInquiries as $inquiry): ?><form method="post" action="<?= site_url('product-inquiries/' . $inquiry['id'] . '/status') ?>"><input type="hidden" name="csrfToken" value="<?= esc($dashboardToken) ?>"><div><strong><?= esc($inquiry['product_name']) ?></strong><span><?= esc($inquiry['customer_name']) ?> &middot; <?= esc($inquiry['email']) ?> &middot; <?= esc($inquiry['phone']) ?></span></div><select name="status"><option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : '' ?>>New</option><option value="reviewing" <?= $inquiry['status'] === 'reviewing' ? 'selected' : '' ?>>Reviewing</option><option value="quoted" <?= $inquiry['status'] === 'quoted' ? 'selected' : '' ?>>Quoted</option><option value="closed" <?= $inquiry['status'] === 'closed' ? 'selected' : '' ?>>Closed</option></select><button type="submit">Save</button></form><?php endforeach; ?></div><?php endif; ?>
      </section>
    <?php endif; ?>

    <?php if (in_array($role, ['employee', 'admin'], true)): ?>
      <section class="communication-section" id="communication">
        <div class="communication-copy"><p class="eyebrow">Internal communication</p><h2>Work with the right department, right here.</h2><p>Messages stay within Sun Son Solar. Send updates to one department or share an operations message with all departments.</p><div class="active-people"><p>Active portal accounts</p><?php foreach ($activeDirectory as $person): ?><span class="person-chip <?= (int) $person['is_online'] === 1 ? 'is-online' : '' ?>"><i></i><?= esc(trim(($person['first_name'] ?? '') . ' ' . ($person['last_name'] ?? '')) ?: $person['username']) ?><small><?= esc($person['department'] ?: 'Administration') ?></small></span><?php endforeach; ?></div></div>
        <form class="message-form" method="post" action="<?= site_url('department-messages') ?>"><input type="hidden" name="csrfToken" value="<?= esc($dashboardToken) ?>"><label>To department<select name="recipientDepartment" required><?php foreach ($departmentOptions as $option): ?><option value="<?= esc($option) ?>" <?= old('recipientDepartment') === $option ? 'selected' : '' ?>><?= esc($option) ?></option><?php endforeach; ?></select></label><label>Subject<input name="subject" maxlength="180" value="<?= esc(old('subject')) ?>" placeholder="Short update title" required></label><label>Message<textarea name="body" maxlength="4000" placeholder="Write a clear department update..." required><?= esc(old('body')) ?></textarea></label><button type="submit">Send department message <span aria-hidden="true">&#8594;</span></button></form>
      </section>
      <section class="messages-feed" aria-labelledby="messageFeedTitle"><div class="data-section-heading"><div><p class="eyebrow">Team channel</p><h2 id="messageFeedTitle">Recent department messages.</h2></div><span class="section-count"><?= esc($department ?: 'All departments') ?></span></div><?php if ($departmentMessages === []): ?><p class="empty-data">No message has been sent to this channel yet.</p><?php else: ?><div class="message-list"><?php foreach ($departmentMessages as $message): ?><article><div><strong><?= esc($message['subject']) ?></strong><span><?= esc($message['sender_department']) ?> to <?= esc($message['recipient_department']) ?> · <?= esc(date('M j, g:i a', strtotime($message['created_at']))) ?></span></div><p><?= esc($message['body']) ?></p></article><?php endforeach; ?></div><?php endif; ?></section>
    <?php endif; ?>

    <?php if ($role === 'admin'): ?>
      <section class="analytics-section" id="admin-content"><div class="data-section-heading"><div><p class="eyebrow">Reports and analytics</p><h2>What is happening across operations.</h2></div><span class="section-count">Live database totals</span></div><div class="analytics-grid"><article><span>New inquiries today</span><strong><?= esc((string) $analytics['inquiriesToday']) ?></strong></article><article><span>Open product inquiries</span><strong><?= esc((string) $analytics['openInquiries']) ?></strong></article><article><span>Active employee accounts</span><strong><?= esc((string) $analytics['activeEmployees']) ?></strong></article><article><span>Open support tickets</span><strong><?= esc((string) $analytics['openTickets']) ?></strong></article></div></section>
      <section class="data-section" id="inquiries"><div class="data-section-heading"><div><p class="eyebrow">Product inquiry management</p><h2>Route customer product interest.</h2></div><span class="section-count"><?= count($allInquiries) ?> total</span></div><?php if ($allInquiries === []): ?><p class="empty-data">No product inquiry has arrived yet.</p><?php else: ?><div class="inquiry-management"><?php foreach ($allInquiries as $inquiry): ?><form method="post" action="<?= site_url('product-inquiries/' . $inquiry['id'] . '/status') ?>"><input type="hidden" name="csrfToken" value="<?= esc($dashboardToken) ?>"><div><strong><?= esc($inquiry['product_name']) ?></strong><span><?= esc($inquiry['customer_name']) ?> · <?= esc($inquiry['email']) ?> · <?= esc($inquiry['phone']) ?></span></div><select name="status"><option value="new" <?= $inquiry['status'] === 'new' ? 'selected' : '' ?>>New</option><option value="reviewing" <?= $inquiry['status'] === 'reviewing' ? 'selected' : '' ?>>Reviewing</option><option value="quoted" <?= $inquiry['status'] === 'quoted' ? 'selected' : '' ?>>Quoted</option><option value="closed" <?= $inquiry['status'] === 'closed' ? 'selected' : '' ?>>Closed</option></select><button type="submit">Save</button></form><?php endforeach; ?></div><?php endif; ?></section>
      <section class="staff-section" id="staff-account">
        <div class="section-copy"><p class="eyebrow">HR and administration</p><h2>Create an employee account.</h2><p>Employee roles are assigned here by authorized staff. Set the account to <strong>Active</strong> only when the employee is ready to sign in.</p></div>
        <form class="staff-form" method="post" action="<?= site_url('staff') ?>">
          <input type="hidden" name="csrfToken" value="<?= esc($staffToken) ?>">
          <?php if (session('staff_error')): ?><p class="form-alert error" role="alert"><?= esc(session('staff_error')) ?></p><?php endif; ?>
          <?php if (session('staff_success')): ?><p class="form-alert success" role="status"><?= esc(session('staff_success')) ?></p><?php endif; ?>
          <div class="field-grid three"><label>First name<input name="firstName" value="<?= esc(old('firstName')) ?>" required></label><label>Middle name<input name="middleName" value="<?= esc(old('middleName')) ?>"></label><label>Last name<input name="lastName" value="<?= esc(old('lastName')) ?>" required></label></div>
          <div class="field-grid three"><label>Birthdate<input name="birthdate" type="date" value="<?= esc(old('birthdate')) ?>" required></label><label>Gender<select name="gender" required><option value="">Select</option><?php foreach (['Female', 'Male', 'Other'] as $gender): ?><option value="<?= $gender ?>" <?= old('gender') === $gender ? 'selected' : '' ?>><?= $gender ?></option><?php endforeach; ?></select></label><label>Department<select name="department" required><option value="">Select</option><?php foreach (['Administration', 'IT', 'Despatch', 'Accounting', 'HR', 'Marketing Sales', 'Customer Service'] as $department): ?><option value="<?= esc($department) ?>" <?= old('department') === $department ? 'selected' : '' ?>><?= esc($department) ?></option><?php endforeach; ?></select></label></div>
          <div class="field-grid"><label>Email address<input name="email" type="email" value="<?= esc(old('email')) ?>" required></label><label>Phone number<input name="phone" type="tel" value="<?= esc(old('phone')) ?>" required></label></div>
          <label>Office or home address<textarea name="address" required><?= esc(old('address')) ?></textarea></label>
          <div class="field-grid three"><label>Username<input name="username" value="<?= esc(old('username')) ?>" required></label><label>Temporary password<input name="password" type="password" autocomplete="new-password" required></label><label>Account status<select name="accountStatus" required><option value="active" <?= old('accountStatus', 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="pending" <?= old('accountStatus') === 'pending' ? 'selected' : '' ?>>Pending</option><option value="inactive" <?= old('accountStatus') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></label></div>
          <button type="submit">Create employee account <span aria-hidden="true">&#8594;</span></button>
        </form>
      </section>

      <section class="staff-list" aria-labelledby="employeeAccessTitle">
        <div><p class="eyebrow">Employee access</p><h2 id="employeeAccessTitle">Manage staff sign in.</h2><p>Activate a staff member and set a new password before sharing their sign-in details.</p></div>
        <div class="staff-rows">
          <?php foreach ($staffAccounts as $staff): ?>
            <form class="staff-row" method="post" action="<?= site_url('staff/' . $staff['id']) ?>">
              <input type="hidden" name="csrfToken" value="<?= esc($staffToken) ?>">
              <div class="staff-identity"><strong><?= esc($staff['first_name'] . ' ' . $staff['last_name']) ?></strong><span><?= esc($staff['email']) ?> &middot; <?= esc($staff['username']) ?></span></div>
              <label>Status<select name="accountStatus"><option value="active" <?= $staff['account_status'] === 'active' ? 'selected' : '' ?>>Active</option><option value="pending" <?= $staff['account_status'] === 'pending' ? 'selected' : '' ?>>Pending</option><option value="inactive" <?= $staff['account_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></label>
              <label>Department<select name="department"><?php foreach (['Administration', 'IT', 'Despatch', 'Accounting', 'HR', 'Marketing Sales', 'Customer Service'] as $department): ?><option value="<?= esc($department) ?>" <?= $staff['department'] === $department ? 'selected' : '' ?>><?= esc($department) ?></option><?php endforeach; ?></select></label>
              <label>New password <small>Optional</small><input name="newPassword" type="password" autocomplete="new-password" placeholder="Keep current password"></label>
              <button type="submit">Save access</button>
            </form>
          <?php endforeach; ?>
          <?php if ($staffAccounts === []): ?><p class="empty-staff">No employee account has been created yet.</p><?php endif; ?>
        </div>
      </section>
    <?php endif; ?>
  </main>
  <script src="<?= base_url('assets/js/portal.js') ?>"></script>
</body>
</html>
