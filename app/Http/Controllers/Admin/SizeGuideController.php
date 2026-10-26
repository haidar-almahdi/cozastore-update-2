<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SizeGuide;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class SizeGuideController extends Controller
{
    public function index(Request $request)
    {
        $guides = SizeGuide::query()
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();
        return view('admin.size-guides.index', compact('guides'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        $products = Product::orderBy('name')->get(['id','name','slug']);
        return view('admin.size-guides.create', compact('categories','products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'scope_type' => 'required|in:product,category',
            'scope_id' => 'required|integer',
            'gender' => 'nullable|string|max:50',
            'unit' => 'required|in:cm,inch',
            'table_schema' => 'nullable|string',
            'rows' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Decode JSON safely with validation
        $schemaJson = $data['table_schema'] ?? null;
        $rowsJson = $data['rows'] ?? null;
        $schema = null;
        $rows = null;
        if ($schemaJson) {
            $schema = json_decode($schemaJson, true);
            if (!is_array($schema)) {
                return back()->withInput()->withErrors(['table_schema' => 'Invalid JSON in Columns Schema.']);
            }
        }
        if ($rowsJson) {
            $rows = json_decode($rowsJson, true);
            if (!is_array($rows)) {
                return back()->withInput()->withErrors(['rows' => 'Invalid JSON in Rows.']);
            }
        }

        $payload = [
            'name' => $data['name'],
            'scope_type' => $data['scope_type'],
            'scope_id' => $data['scope_id'],
            'gender' => $data['gender'] ?? null,
            'unit' => $data['unit'],
            'table_schema' => $schema,
            'rows' => $rows,
            'is_active' => $request->boolean('is_active', true),
        ];

        SizeGuide::create($payload);
        return redirect()->route('admin.size-guides.index')->with('success', 'Size guide created.');
    }

    public function edit(SizeGuide $sizeGuide)
    {
        $categories = Category::orderBy('name')->get(['id','name']);
        $products = Product::orderBy('name')->get(['id','name','slug']);
        return view('admin.size-guides.edit', [
            'guide' => $sizeGuide,
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function update(Request $request, SizeGuide $sizeGuide)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'scope_type' => 'required|in:product,category',
            'scope_id' => 'required|integer',
            'gender' => 'nullable|string|max:50',
            'unit' => 'required|in:cm,inch',
            'table_schema' => 'nullable|string',
            'rows' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        // Decode JSON safely; if invalid, do not overwrite and return errors
        $schemaJson = $data['table_schema'] ?? null;
        $rowsJson = $data['rows'] ?? null;
        $schema = null;
        $rows = null;
        if ($schemaJson) {
            $schema = json_decode($schemaJson, true);
            if (!is_array($schema)) {
                return back()->withInput()->withErrors(['table_schema' => 'Invalid JSON in Columns Schema.']);
            }
        }
        if ($rowsJson) {
            $rows = json_decode($rowsJson, true);
            if (!is_array($rows)) {
                return back()->withInput()->withErrors(['rows' => 'Invalid JSON in Rows.']);
            }
        }

        $sizeGuide->update([
            'name' => $data['name'],
            'scope_type' => $data['scope_type'],
            'scope_id' => $data['scope_id'],
            'gender' => $data['gender'] ?? null,
            'unit' => $data['unit'],
            'table_schema' => $schemaJson !== null ? $schema : $sizeGuide->table_schema,
            'rows' => $rowsJson !== null ? $rows : $sizeGuide->rows,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.size-guides.index')->with('success', 'Size guide updated.');
    }

    public function destroy(SizeGuide $sizeGuide)
    {
        $sizeGuide->delete();
        return redirect()->route('admin.size-guides.index')->with('success', 'Size guide deleted.');
    }

    public function duplicate(SizeGuide $sizeGuide)
    {
        $copy = $sizeGuide->replicate();
        $copy->name = $sizeGuide->name . ' (Copy)';
        $copy->is_active = false;
        $copy->push();

        return redirect()->route('admin.size-guides.edit', $copy)
            ->with('success', 'Size guide duplicated. You can now edit it.');
    }

    public function generateDefaults()
    {
        $categories = Category::orderBy('name')->get(['id','name']);

        $created = 0; $skipped = 0;

        $clothingSchema = [
            ['key'=>'size','label'=>'Size'],
            ['key'=>'chest','label'=>'Chest (cm)'],
            ['key'=>'waist','label'=>'Waist (cm)'],
            ['key'=>'hips','label'=>'Hips (cm)'],
            ['key'=>'shoulder','label'=>'Shoulder (cm)'],
            ['key'=>'sleeve','label'=>'Sleeve Length (cm)'],
            ['key'=>'length','label'=>'Length (cm)'],
        ];
        $clothingRows = [
            ['size'=>'XS','chest'=>86,'waist'=>70,'hips'=>90,'shoulder'=>41,'sleeve'=>61,'length'=>66],
            ['size'=>'S','chest'=>92,'waist'=>76,'hips'=>96,'shoulder'=>43,'sleeve'=>62,'length'=>68],
            ['size'=>'M','chest'=>96,'waist'=>80,'hips'=>100,'shoulder'=>44,'sleeve'=>63,'length'=>70],
            ['size'=>'L','chest'=>102,'waist'=>86,'hips'=>106,'shoulder'=>46,'sleeve'=>64,'length'=>72],
            ['size'=>'XL','chest'=>108,'waist'=>92,'hips'=>112,'shoulder'=>48,'sleeve'=>65,'length'=>74],
        ];

        $shoesSchema = [
            ['key'=>'eu','label'=>'EU'],
            ['key'=>'us','label'=>'US'],
            ['key'=>'uk','label'=>'UK'],
            ['key'=>'foot_length','label'=>'Foot Length (cm)'],
        ];
        $shoesRows = [
            ['eu'=>38,'us'=>6,'uk'=>5.5,'foot_length'=>24.0],
            ['eu'=>39,'us'=>7,'uk'=>6.5,'foot_length'=>24.6],
            ['eu'=>40,'us'=>7.5,'uk'=>7,'foot_length'=>25.3],
            ['eu'=>41,'us'=>8,'uk'=>7.5,'foot_length'=>26.0],
            ['eu'=>42,'us'=>9,'uk'=>8,'foot_length'=>26.6],
            ['eu'=>43,'us'=>10,'uk'=>9,'foot_length'=>27.3],
        ];

        $accessoriesSchema = [
            ['key'=>'size','label'=>'Size'],
            ['key'=>'circumference','label'=>'Circumference (cm)'],
            ['key'=>'width','label'=>'Width (cm)'],
            ['key'=>'length','label'=>'Length (cm)'],
        ];
        $accessoriesRows = [
            ['size'=>'One Size','circumference'=>56,'width'=>8,'length'=>180],
            ['size'=>'Small','circumference'=>54,'width'=>7,'length'=>170],
            ['size'=>'Medium','circumference'=>56,'width'=>8,'length'=>180],
            ['size'=>'Large','circumference'=>58,'width'=>9,'length'=>190],
        ];

        foreach ($categories as $cat) {
            // Clothing per category
            $existsClothing = SizeGuide::where('scope_type','category')->where('scope_id',$cat->id)
                ->where('name','LIKE','%Clothing%')->exists();
            if (!$existsClothing) {
                SizeGuide::create([
                    'name' => $cat->name . ' - Clothing',
                    'scope_type' => 'category',
                    'scope_id' => $cat->id,
                    'gender' => null,
                    'unit' => 'cm',
                    'table_schema' => $clothingSchema,
                    'rows' => $clothingRows,
                    'is_active' => true,
                ]);
                $created++;
            } else { $skipped++; }

            // Shoes per category
            $existsShoes = SizeGuide::where('scope_type','category')->where('scope_id',$cat->id)
                ->where('name','LIKE','%Shoes%')->exists();
            if (!$existsShoes) {
                SizeGuide::create([
                    'name' => $cat->name . ' - Shoes',
                    'scope_type' => 'category',
                    'scope_id' => $cat->id,
                    'gender' => null,
                    'unit' => 'cm',
                    'table_schema' => $shoesSchema,
                    'rows' => $shoesRows,
                    'is_active' => true,
                ]);
                $created++;
            } else { $skipped++; }

            // Accessories per category
            $existsAcc = SizeGuide::where('scope_type','category')->where('scope_id',$cat->id)
                ->where('name','LIKE','%Accessories%')->exists();
            if (!$existsAcc) {
                SizeGuide::create([
                    'name' => $cat->name . ' - Accessories',
                    'scope_type' => 'category',
                    'scope_id' => $cat->id,
                    'gender' => null,
                    'unit' => 'cm',
                    'table_schema' => $accessoriesSchema,
                    'rows' => $accessoriesRows,
                    'is_active' => true,
                ]);
                $created++;
            } else { $skipped++; }
        }

        return redirect()->route('admin.size-guides.index')
            ->with('success', "Default size guides generated. Created: {$created}, Skipped existing: {$skipped}.");
    }
}
