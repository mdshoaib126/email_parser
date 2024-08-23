<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuccessfulEmail;

class ParseEmails extends Command
{
    // Command signature
    protected $signature = 'emails:parse';

    // Command description
    protected $description = 'Parse raw email content and extract the plain text body';

    // Execute the command
    public function handle()
    {
        // Retrieve all emails that have not been processed (assuming raw_text is null if unprocessed)
        $unprocessedEmails = SuccessfulEmail::whereNull('raw_text')->get();

        foreach ($unprocessedEmails as $email) {
            // Extract plain text content from the raw email
            $plainTextBody = $this->extractBodyContent($email->email);
            $plainText = $this->extractPlainTextFromEmail($email->email);

            // Update the email record with the extracted plain text
            $email->raw_text = $plainTextBody;
            $email->save();

            $this->info("Processed email ID: {$email->id}");
        }

        $this->info('All unprocessed emails have been parsed successfully.');
    }
 
    private function extractBodyContent($rawEmailContent)
    {
        $bodyContent = '';

        if (preg_match("/<body[^>]*>(.*?)<\/body>/is", $rawEmailContent, $matches)) {
            $bodyContent = $matches[1];
        }

        $plainText = strip_tags($bodyContent);
        $plainText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plainText = preg_replace("/\r\n|\r/", "\n", $plainText);
        $plainText = preg_replace('/\s+/', ' ', $plainText);
        $plainText = preg_replace('/\n+/', "\n", $plainText);
        $plainText = trim($plainText);

        return $plainText;
    }
 
    private function extractPlainTextFromEmail($rawEmail)
    {
        $plainText = '';

        $parts = preg_split("/\r\n\r\n/", $rawEmail, 2);
        if (isset($parts[0])) {
            $body = $parts[0];

            if (preg_match('/Content-Type: multipart\/alternative; boundary="(.+?)"/', $rawEmail, $matches)) {
                $boundary = $matches[1];
                $sections = preg_split("/--" . preg_quote($boundary, '/') . "/", $body);

                foreach ($sections as $section) {
                    if (strpos($section, 'Content-Type: text/plain') !== false) {
                        $plainTextPart = preg_split("/\r\n\r\n/", $section, 2)[1] ?? '';
                        $plainText = strip_tags($plainTextPart);
                        break;
                    }
                }
            } else {
                $plainText = strip_tags($body);
            }

            $plainText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $plainText = preg_replace('/[^\PC\n]/u', ' ', $plainText);
            $plainText = preg_replace("/\r\n|\r/", "\n", $plainText);
            $plainText = preg_replace('/\s+/', ' ', $plainText);
            $plainText = preg_replace('/\n+/', "\n", $plainText);
            $plainText = trim($plainText);
        }

        return $plainText;
    }
}
