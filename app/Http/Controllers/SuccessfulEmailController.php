<?php

namespace App\Http\Controllers;

use App\Models\SuccessfulEmail;
use Illuminate\Http\Request;

class SuccessfulEmailController extends Controller
{
    public function store(Request $request)
    {
        // Extract email content from the request
        $rawEmailContent = $request->input('email');

        $plainTextBody = $this->extractBodyContent($rawEmailContent);
        $plainText = $this->extractPlainTextFromEmail($rawEmailContent);
        
        $emailRecord = SuccessfulEmail::create([
            'email' => $plainText,
            'raw_text' => $plainTextBody,
            // Other fields can be filled here as needed
        ]);

        return response()->json($emailRecord, 201);
    }

    // Get a specific email record by ID
    public function show($id)
    {
        $email = SuccessfulEmail::findOrFail($id);
        return response()->json($email);
    }

    // Update a specific email record by ID
    public function update(Request $request, $id)
    {
        $email = SuccessfulEmail::findOrFail($id);

        $rawEmailContent = $request->input('email');
        $plainTextBody = $this->extractBodyContent($rawEmailContent);
        $plainText = $this->extractPlainTextFromEmail($rawEmailContent);
        $email->email = $rawEmailContent;
        $email->raw_text = $plainTextBody;

        $email->save();

        return response()->json($email);
    }

    // Get all email records excluding soft-deleted items
    public function index()
    {
        $emails = SuccessfulEmail::whereNull('deleted_at')->get();
        return response()->json($emails);
    }

    // Soft delete a specific email record by ID
    public function destroy($id)
    {
        $email = SuccessfulEmail::findOrFail($id);
        $email->delete();
        return response()->json(['message' => 'Email deleted successfully']);
    }

    private function extractBodyContent($rawEmailContent)
    {
        // Remove everything before the actual body tag
        $bodyContent = '';

        if (preg_match("/<body[^>]*>(.*?)<\/body>/is", $rawEmailContent, $matches)) {
            $bodyContent = $matches[1];
        }

        // Remove all HTML tags
        $plainText = strip_tags($bodyContent);

        // Decode HTML entities
        $plainText = html_entity_decode($plainText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Normalize line breaks (\r\n to \n)
        $plainText = preg_replace("/\r\n|\r/", "\n", $plainText);

        // Replace multiple whitespace or line breaks with a single space or line break
        $plainText = preg_replace('/\s+/', ' ', $plainText);
        $plainText = preg_replace('/\n+/', "\n", $plainText);

        // Trim leading and trailing whitespace
        $plainText = trim($plainText);

        // Ensure only printable characters and line breaks are in the output
        $plainText = preg_replace('/[^\P{C}\n]+/u', '', $plainText);

        return $plainText;
    }

    private function extractPlainTextFromEmail($rawEmail)
    {
        $plainText = '';

        // Split headers and body
        $parts = preg_split("/\r\n\r\n/", $rawEmail, 2);
        if (isset($parts[0])) {
            $body = $parts[0];

            // Check for multi-part content and prioritize plain text parts
            if (preg_match('/Content-Type: multipart\/alternative; boundary="(.+?)"/', $rawEmail, $matches)) {
                $boundary = $matches[1];
                $sections = preg_split("/--" . preg_quote($boundary, '/') . "/", $body);

                foreach ($sections as $section) {
                    // Look for the plain text part
                    if (strpos($section, 'Content-Type: text/plain') !== false) {
                        $plainTextPart = preg_split("/\r\n\r\n/", $section, 2)[1] ?? '';
                        $plainText = strip_tags($plainTextPart);
                        break;
                    }
                }
            } else {
                // If not multi-part, assume it's just a single body
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
