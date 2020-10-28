<?php

namespace App\Models;

class BugReport extends Model
{

    private $id;
    private $report_type;
    private $link;
    private $email;
    private $message;
    private $created_at;

    public function getId(): int
    {
        return $this->id;
    }

    public function setReportType(string $reportType)
    {
        $this->report_type = $reportType;
        return $this;
    }

    public function getReportType(): string
    {
        return $this->report_type;
    }

    public function setMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setLink(string $link)
    {
        $this->link = $link;
        return $this;
    }

    public function getLink(): string
    {
        return $this->link;
    }


    public function toArray(): array
    {
        return [
            'report_type' => $this->getReportType(),
            'email' => $this->getEmail(),
            'link' => $this->getLink(),
            'message' => $this->getMessage(),
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
    // public function create(array $data);
    // public function update(array $data);
    // public function delete();
}
