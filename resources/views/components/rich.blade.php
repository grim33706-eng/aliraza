@props(['text' => ''])
{{-- Escaped text with light formatting: *accent* → gradient <em>, **bold** → <strong>, :year → current year. --}}
{!! preg_replace(['/\*\*(.+?)\*\*/', '/\*(.+?)\*/'], ['<strong>$1</strong>', '<em>$1</em>'], str_replace(':year', date('Y'), e($text))) !!}
