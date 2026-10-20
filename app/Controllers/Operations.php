<?php

namespace App\Controllers;

class Operations extends BaseController
{
    public function checkIn()
    {
        $userId = $this->employeeId();
        if ($userId === null || ! $this->hasDashboardToken()) {
            return redirect()->to(site_url('login'));
        }

        $requestId = (int) $this->request->getPost('serviceRequestId');
        $latitude = filter_var($this->request->getPost('latitude'), FILTER_VALIDATE_FLOAT);
        $longitude = filter_var($this->request->getPost('longitude'), FILTER_VALIDATE_FLOAT);
        if (! $this->ownsAssignedRequest($requestId, $userId) || $latitude === false || $longitude === false || $latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return redirect()->to(site_url('dashboard') . '#assigned-work')->with('portal_error', 'Choose one of your assigned services and provide a valid site location.');
        }

        db_connect()->table('technician_checkins')->insert([
            'service_request_id' => $requestId,
            'employee_user_id' => $userId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy_m' => filter_var($this->request->getPost('accuracy'), FILTER_VALIDATE_FLOAT) ?: null,
            'checked_in_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('dashboard') . '#assigned-work')->with('portal_success', 'Site check-in recorded.');
    }

    public function submitReport()
    {
        $userId = $this->employeeId();
        if ($userId === null || ! $this->hasDashboardToken()) {
            return redirect()->to(site_url('login'));
        }

        $requestId = (int) $this->request->getPost('serviceRequestId');
        $type = (string) $this->request->getPost('reportType');
        $summary = trim((string) $this->request->getPost('summary'));
        if (! $this->ownsAssignedRequest($requestId, $userId) || ! in_array($type, ['completion', 'issue', 'customer_concern', 'safety'], true) || $summary === '' || strlen($summary) > 4000) {
            return redirect()->to(site_url('dashboard') . '#field-report')->withInput()->with('portal_error', 'Choose one of your assigned services, a report type, and a report up to 4,000 characters.');
        }

        db_connect()->table('technician_service_reports')->insert([
            'service_request_id' => $requestId,
            'employee_user_id' => $userId,
            'report_type' => $type,
            'summary' => $summary,
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(site_url('dashboard') . '#field-report')->with('portal_success', 'Service report sent to operations.');
    }

    private function employeeId(): ?int
    {
        $id = (int) session()->get('auth_user_id');
        return session()->get('auth_role') === 'employee' && $id > 0 ? $id : null;
    }

    private function ownsAssignedRequest(int $requestId, int $userId): bool
    {
        return $requestId > 0 && db_connect()->table('service_requests')->where('id', $requestId)->where('assigned_employee_id', $userId)->countAllResults() === 1;
    }

    private function hasDashboardToken(): bool
    {
        $sessionToken = (string) session()->get('dashboard_csrf_token');
        $token = (string) $this->request->getPost('csrfToken');
        return $sessionToken !== '' && hash_equals($sessionToken, $token);
    }
}
