#!/usr/bin/env python3
"""Regenerate assets/css/lib/bootstrap-subset.css from bootstrap.min.css.

Keeps only the rules whose selectors match a class the dashboard actually
renders (classes.txt) plus the state classes Bootstrap's JS toggles at
runtime. Re-run after adding views or component classes:

    tools/scan-classes.sh > tools/classes.txt
    python3 tools/purge-bootstrap.py
"""
import re

src = open('assets/css/lib/bootstrap.min.css', encoding='utf-8').read()
used = set(l.strip() for l in open('tools/classes.txt', encoding='utf-8') if l.strip())
SAFE = set("""show fade collapsing collapse active disabled modal-open modal-backdrop modal-dialog modal-dialog-centered modal-dialog-scrollable modal-content modal-header modal-body modal-footer modal-title dropdown-menu dropdown-toggle dropdown-item dropdown-divider toast-header toast-body toast-container accordion-button accordion-collapse accordion-body accordion-item accordion-header nav-link nav-item nav-tabs nav-pills tab-pane tab-content alert-heading alert-link progress-bar progress-bar-striped progress-bar-animated spinner-border spinner-grow form-control form-select form-check form-check-input form-check-label form-label input-group input-group-text invalid-feedback valid-feedback is-invalid is-valid was-validated btn-close btn-group btn-group-sm btn-group-lg table-responsive table-striped table-hover table-bordered list-group list-group-item list-group-flush breadcrumb-item rounded-pill visually-hidden sr-only icon text-lg text-md text-sm text-xl dropdown-menu-lg dropdown-menu-sm""".split())

def classes_of(sel):
    return set(re.findall(r'\.([A-Za-z0-9_-]+)', sel))

def keep(sel):
    s = sel.strip()
    if not s: return False
    if ':root' in s: return True
    c = classes_of(s)
    if not c: return True
    return bool(c & used) or bool(c & SAFE)

def parse(text):
    out = []; i = 0; n = len(text)
    while i < n:
        j = text.find('{', i)
        if j < 0: break
        head = text[i:j]
        # trim anything left over before this rule (comments, stray braces)
        head = head[head.rfind('}')+1:] if '}' in head else head
        head = re.sub(r'/\*.*?\*/', '', head, flags=re.S).strip()
        depth = 1; k = j + 1
        while k < n and depth:
            if text[k] == '{': depth += 1
            elif text[k] == '}': depth -= 1
            k += 1
        inner = text[j+1:k-1]
        if head.startswith('@media') or head.startswith('@supports'):
            sub = parse(inner)
            if sub.strip(): out.append(head + '{' + sub + '}')
        elif head.startswith('@keyframes') or head.startswith('@-webkit-keyframes'):
            out.append(head + '{' + inner + '}')
        elif keep(head):
            out.append(head + '{' + inner + '}')
        i = k
    return ''.join(out)

res = parse(src)
res = re.sub(r'\s+', ' ', res).replace(' }', '}').replace('{ ', '{')
open('assets/css/lib/bootstrap-subset.css','w',encoding='utf-8').write(res)
print('in :', len(src)//1024, 'KB')
print('out:', len(res)//1024, 'KB')
