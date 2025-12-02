from pathlib import Path
text = Path('public/index_v2.php').read_text('cp1252')
needle = '<nav class="hidden md:flex items-center gap-4 text-sm">'
if needle not in text:
    raise SystemExit('nav not found')
start = text.index(needle)
end = text.index('</nav>', start) + len('</nav>')
print(text[start:end])
