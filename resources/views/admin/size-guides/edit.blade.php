@extends('layouts.admin')

@section('title', 'Edit Size Guide')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h1 class="page-title"><i class="fa fa-edit"></i> Edit Size Guide</h1>
    <p class="page-subtitle">Update the size reference</p>
  </div>
  <a href="{{ route('admin.size-guides.index') }}" class="btn btn-outline-secondary"><i class="fa fa-arrow-left"></i> Back</a>
</div>

<div class="card">
  <div class="card-header"><h5 class="mb-0">Guide Details</h5></div>
  <div class="card-body">
    <form method="POST" action="{{ route('admin.size-guides.update', $guide) }}">
      @csrf
      @method('PUT')
      <div class="form-row">
        <div class="form-group col-md-6">
          <label>Name</label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $guide->name) }}" required>
        </div>
        <div class="form-group col-md-3">
          <label>Scope Type</label>
          <select name="scope_type" id="scope_type" class="form-control" required>
            <option value="category" {{ old('scope_type', $guide->scope_type) === 'category' ? 'selected' : '' }}>Category</option>
            <option value="product" {{ old('scope_type', $guide->scope_type) === 'product' ? 'selected' : '' }}>Product</option>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label>Scope Target</label>
          <select name="scope_id" id="scope_id" class="form-control" required>
            <optgroup label="Categories">
              @foreach($categories as $c)
                <option value="{{ $c->id }}" data-scope="category" {{ old('scope_id', $guide->scope_id) == $c->id && old('scope_type', $guide->scope_type) === 'category' ? 'selected' : '' }}>[Category] {{ $c->name }}</option>
              @endforeach
            </optgroup>
            <optgroup label="Products">
              @foreach($products as $p)
                <option value="{{ $p->id }}" data-scope="product" {{ old('scope_id', $guide->scope_id) == $p->id && old('scope_type', $guide->scope_type) === 'product' ? 'selected' : '' }}>[Product] {{ $p->name }}</option>
              @endforeach
            </optgroup>
          </select>
          <small class="text-muted">Ensure Scope Type matches selection</small>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-3">
          <label>Gender (optional)</label>
          <input type="text" name="gender" class="form-control" value="{{ old('gender', $guide->gender) }}" placeholder="men / women / unisex">
        </div>
        <div class="form-group col-md-3">
          <label>Unit</label>
          <select name="unit" class="form-control" required>
            <option value="cm" {{ old('unit', $guide->unit) === 'cm' ? 'selected' : '' }}>CM</option>
            <option value="inch" {{ old('unit', $guide->unit) === 'inch' ? 'selected' : '' }}>Inch</option>
          </select>
        </div>
        <div class="form-group col-md-3">
          <label>Status</label>
          <select name="is_active" class="form-control">
            <option value="1" {{ old('is_active', $guide->is_active) ? 'selected' : '' }}>Active</option>
            <option value="0" {{ !old('is_active', $guide->is_active) ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group col-md-4">
          <label>Template</label>
          <div class="d-flex align-items-center gap-2">
            <select id="template_selector" class="form-control">
              <option value="">None</option>
              <option value="clothing">Clothing (Tops/Bottoms)</option>
              <option value="shoes_men">Shoes (Men)</option>
              <option value="shoes_women">Shoes (Women)</option>
              <optgroup label="Accessories">
                <option value="acc_belts">Belts</option>
                <option value="acc_hats">Hats</option>
                <option value="acc_scarves">Scarves</option>
                <option value="acc_rings">Rings</option>
                <option value="acc_bracelets">Bracelets/Watches</option>
                <option value="acc_bags">Bags</option>
              </optgroup>
            </select>
            <button type="button" id="apply_template" class="btn btn-info">Apply Template</button>
          </div>
        </div>
      </div>

      <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
          <h5 class="mb-0"><i class="fa fa-table"></i> Visual Table Builder</h5>
          <div class="d-flex align-items-center" style="gap:8px;">
            <input type="text" id="new_col_key" class="form-control form-control-sm" placeholder="column_key" style="width:160px;">
            <input type="text" id="new_col_label" class="form-control form-control-sm" placeholder="Column Label" style="width:200px;">
            <button type="button" id="add_column" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus"></i> Add Column</button>
            <button type="button" id="add_row" class="btn btn-sm btn-outline-success"><i class="fa fa-plus"></i> Add Row</button>
          </div>
        </div>
        <div class="card-body" id="builder_container">
          <div class="alert alert-light mb-2">Use the controls above to add/remove columns and rows. Edit cells directly. Data syncs to JSON on save.</div>
          <div class="table-responsive">
            <table class="table table-bordered" id="builder_table">
              <thead>
                <tr id="builder_header">
                  <th style="min-width:120px;">Row</th>
                </tr>
              </thead>
              <tbody id="builder_body"></tbody>
            </table>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Columns Schema (JSON)</label>
        <textarea name="table_schema" class="form-control" rows="4">{{ old('table_schema', json_encode($guide->table_schema, JSON_PRETTY_PRINT)) }}</textarea>
      </div>

      <div class="form-group">
        <label>Rows (JSON)</label>
        <textarea name="rows" class="form-control" rows="6">{{ old('rows', json_encode($guide->rows, JSON_PRETTY_PRINT)) }}</textarea>
      </div>

      <div class="text-right">
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('styles')
<style>
  #builder_container .table td { position: relative; }
  #builder_container .table td input.form-control { 
    pointer-events: auto; position: relative; z-index: 2; background: #fff; 
    padding: .25rem .5rem; height: 32px;
  }
  #builder_container .table thead th { z-index: 1; position: relative; }
  .card-header .form-control, .card-header .btn { position: relative; z-index: 3; }
  #builder_container { pointer-events: auto; }
  #builder_container * { pointer-events: auto; }
  #builder_container .table th, #builder_container .table td { vertical-align: middle; }
  @media (max-width: 768px) { #builder_container .table { display: block; overflow-x: auto; } }
}</style>
@endsection

@section('scripts')
<script>
let builderCols = [];
let builderRows = [];

function renderBuilder() {
  const header = document.getElementById('builder_header');
  const body = document.getElementById('builder_body');
  header.innerHTML = '<th style="min-width:120px;">Row</th>' + builderCols.map((c, idx) => `
    <th>
      <div class="d-flex align-items-center justify-content-between">
        <span>${c.label} <small class="text-muted">(${c.key})</small></span>
        <button type="button" class="btn btn-sm btn-light text-danger" onclick="removeColumn(${idx})"><i class=\"fa fa-trash\"></i></button>
      </div>
    </th>`).join('') + '<th style="width:60px;"></th>';
  body.innerHTML = '';
  builderRows.forEach((row, rIdx) => {
    const cells = builderCols.map((c) => {
      const val = row[c.key] ?? '';
      return `<td><input type=\"text\" class=\"form-control form-control-sm\" value=\"${String(val).replace(/"/g,'&quot;')}\" oninput=\"updateCell(${rIdx}, '${c.key}', this.value)\"></td>`;
    }).join('');
    const label = row['size'] ?? row['label'] ?? `#${rIdx+1}`;
    const tr = document.createElement('tr');
    tr.innerHTML = `<td class=\"text-muted\">${label}</td>${cells}<td class=\"text-center\"><button type=\"button\" class=\"btn btn-sm btn-light text-danger\" onclick=\"removeRow(${rIdx})\"><i class='fa fa-trash'></i></button></td>`;
    body.appendChild(tr);
  });
}

function addColumn(key, label) {
  if (!key || !label) return;
  if (builderCols.find(c => c.key === key)) return alert('Column key must be unique');
  builderCols.push({key, label});
  builderRows = builderRows.map(r => ({...r, [key]: r[key] ?? ''}));
  renderBuilder();
}
function removeColumn(idx) {
  const col = builderCols[idx];
  builderCols.splice(idx,1);
  builderRows = builderRows.map(r => { const nr = {...r}; delete nr[col.key]; return nr; });
  renderBuilder();
}
function addRow(defaults={}) {
  const row = {};
  builderCols.forEach(c => row[c.key] = defaults[c.key] ?? '');
  if (defaults.size) row.size = defaults.size;
  builderRows.push(row);
  renderBuilder();
}
function removeRow(idx) {
  builderRows.splice(idx,1);
  renderBuilder();
}
function updateCell(rIdx, key, value) {
  builderRows[rIdx][key] = value;
}
function loadFromJSON(schemaJson, rowsJson) {
  try {
    const cols = JSON.parse(schemaJson || '[]');
    const rows = JSON.parse(rowsJson || '[]');
    if (Array.isArray(cols) && Array.isArray(rows)) {
      builderCols = cols.map(c => ({key: c.key, label: c.label || c.key}));
      builderRows = rows.map(r => ({...r}));
      renderBuilder();
    }
  } catch(e) {}
}
function syncToHiddenTextareas() {
  const schemaField = document.querySelector('textarea[name="table_schema"]');
  const rowsField = document.querySelector('textarea[name="rows"]');
  if (schemaField) schemaField.value = JSON.stringify(builderCols, null, 2);
  if (rowsField) rowsField.value = JSON.stringify(builderRows, null, 2);
}

document.addEventListener('DOMContentLoaded', function(){
  const schemaField = document.querySelector('textarea[name="table_schema"]');
  const rowsField = document.querySelector('textarea[name="rows"]');
  loadFromJSON(schemaField?.value, rowsField?.value);
  const form = document.querySelector('form[action*="size-guides/"]');
  form?.addEventListener('submit', function(){ syncToHiddenTextareas(); });
});

function templates() {
  return {
    clothing: {
      unit: 'cm',
      schema: [
        {key:'size',label:'Size'},
        {key:'chest',label:'Chest (cm)'},
        {key:'waist',label:'Waist (cm)'},
        {key:'hips',label:'Hips (cm)'},
        {key:'shoulder',label:'Shoulder (cm)'},
        {key:'sleeve',label:'Sleeve Length (cm)'},
        {key:'inseam',label:'Inseam (cm)'},
        {key:'length',label:'Length (cm)'}
      ],
      rows: [
        {size:'S',chest:92,waist:76,hips:94,shoulder:43,sleeve:62,inseam:76,length:68},
        {size:'M',chest:96,waist:80,hips:98,shoulder:44,sleeve:63,inseam:78,length:70},
        {size:'L',chest:102,waist:86,hips:104,shoulder:46,sleeve:64,inseam:80,length:72},
        {size:'XL',chest:108,waist:92,hips:110,shoulder:48,sleeve:65,inseam:82,length:74}
      ]
    },
    shoes_men: {
      unit: 'cm',
      schema: [
        {key:'eu',label:'EU'},
        {key:'us',label:'US'},
        {key:'uk',label:'UK'},
        {key:'foot_length',label:'Foot Length (cm)'}
      ],
      rows: [
        {eu:40,us:7,uk:6.5,foot_length:25.3},
        {eu:41,us:8,uk:7.5,foot_length:26.0},
        {eu:42,us:9,uk:8,foot_length:26.6},
        {eu:43,us:10,uk:9,foot_length:27.3},
        {eu:44,us:11,uk:10,foot_length:28.0},
        {eu:45,us:12,uk:11,foot_length:28.6}
      ]
    },
    shoes_women: {
      unit: 'cm',
      schema: [
        {key:'eu',label:'EU'},
        {key:'us',label:'US'},
        {key:'uk',label:'UK'},
        {key:'foot_length',label:'Foot Length (cm)'}
      ],
      rows: [
        {eu:35,us:5,uk:2.5,foot_length:22.0},
        {eu:36,us:6,uk:3.5,foot_length:22.6},
        {eu:37,us:6.5,uk:4,foot_length:23.3},
        {eu:38,us:7.5,uk:5,foot_length:24.0},
        {eu:39,us:8.5,uk:6,foot_length:24.6},
        {eu:40,us:9,uk:6.5,foot_length:25.3}
      ]
    },
    acc_belts: {
      unit: 'cm',
      schema: [
        {key:'size',label:'Size'},
        {key:'waist',label:'Waist (cm)'},
        {key:'total_length',label:'Total Length (cm)'},
        {key:'width',label:'Width (cm)'}
      ],
      rows: [
        {size:'S',waist:70,total_length:95,width:3},
        {size:'M',waist:80,total_length:105,width:3},
        {size:'L',waist:90,total_length:115,width:3.5}
      ]
    },
    acc_hats: {
      unit: 'cm',
      schema: [
        {key:'size',label:'Size'},
        {key:'head_circumference',label:'Head Circumference (cm)'}
      ],
      rows: [
        {size:'S',head_circumference:54},
        {size:'M',head_circumference:56},
        {size:'L',head_circumference:58}
      ]
    },
    acc_scarves: {
      unit: 'cm',
      schema: [
        {key:'size',label:'Size'},
        {key:'length',label:'Length (cm)'},
        {key:'width',label:'Width (cm)'}
      ],
      rows: [
        {size:'Standard',length:180,width:30},
        {size:'Large',length:200,width:40}
      ]
    },
    acc_rings: {
      unit: 'cm',
      schema: [
        {key:'us',label:'US'},
        {key:'uk',label:'UK'},
        {key:'inside_diameter_mm',label:'Inside Diameter (mm)'},
        {key:'finger_circumference_mm',label:'Finger Circumference (mm)'}
      ],
      rows: [
        {us:5,uk:'J 1/2',inside_diameter_mm:15.7,finger_circumference_mm:49.3},
        {us:6,uk:'L 1/2',inside_diameter_mm:16.5,finger_circumference_mm:51.9},
        {us:7,uk:'N 1/2',inside_diameter_mm:17.3,finger_circumference_mm:54.4},
        {us:8,uk:'P 1/2',inside_diameter_mm:18.1,finger_circumference_mm:57.0}
      ]
    },
    acc_bracelets: {
      unit: 'cm',
      schema: [
        {key:'size',label:'Size'},
        {key:'wrist_circumference',label:'Wrist Circumference (cm)'},
        {key:'band_width_mm',label:'Band Width (mm)'}
      ],
      rows: [
        {size:'S',wrist_circumference:15,band_width_mm:18},
        {size:'M',wrist_circumference:17,band_width_mm:20},
        {size:'L',wrist_circumference:19,band_width_mm:22}
      ]
    },
    acc_bags: {
      unit: 'cm',
      schema: [
        {key:'size',label:'Size'},
        {key:'height',label:'Height (cm)'},
        {key:'width',label:'Width (cm)'},
        {key:'depth',label:'Depth (cm)'},
        {key:'strap_drop',label:'Strap Drop (cm)'}
      ],
      rows: [
        {size:'Small',height:15,width:20,depth:7,strap_drop:50},
        {size:'Medium',height:20,width:28,depth:10,strap_drop:55},
        {size:'Large',height:28,width:35,depth:12,strap_drop:60}
      ]
    }
  };
}

document.getElementById('apply_template')?.addEventListener('click', function(){
  var t = document.getElementById('template_selector').value;
  if (!t) return;
  var data = templates()[t];
  if (!data) return;
  var unitSelect = document.querySelector('select[name="unit"]');
  if (unitSelect) unitSelect.value = data.unit;
  const schemaField = document.querySelector('textarea[name="table_schema"]');
  const rowsField = document.querySelector('textarea[name="rows"]');
  if (schemaField) schemaField.value = JSON.stringify(data.schema, null, 2);
  if (rowsField) rowsField.value = JSON.stringify(data.rows, null, 2);
  loadFromJSON(JSON.stringify(data.schema), JSON.stringify(data.rows));
});
document.getElementById('add_column').addEventListener('click', function(){
  const k = document.getElementById('new_col_key').value.trim();
  const l = document.getElementById('new_col_label').value.trim();
  if (!k || !l) return alert('Enter column key and label');
  addColumn(k, l);
  document.getElementById('new_col_key').value = '';
  document.getElementById('new_col_label').value = '';
});
document.getElementById('add_row').addEventListener('click', function(){ addRow({}); });
</script>
@endsection
