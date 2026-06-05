<?php

namespace App\Http\Controllers\Back;

use App\Helpers\ImageHelper;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\ChieldCategory;
use App\Models\Item;
use App\Models\Order;
use App\Models\Subcategory;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

class CsvProductController extends Controller
{
    protected function csvCell($value): string
    {
        if ($value === null) {
            return '';
        }
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }
        if (is_scalar($value)) {
            return (string) $value;
        }
        // Arrays/objects (e.g. casted JSON columns like metal_type) must be serialized.
        $encoded = json_encode($value, JSON_UNESCAPED_UNICODE);
        return $encoded === false ? '' : $encoded;
    }

    protected function normalizeCsvRow(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $out[$k] = $this->csvCell($v);
        }
        return $out;
    }

    protected function streamCsv(array $rows, string $filename)
    {
        $headers = [
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Expires' => '0',
            'Pragma' => 'public',
        ];

        $callback = function () use ($rows) {
            $FH = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fwrite($FH, "\xEF\xBB\xBF");
            foreach ($rows as $row) {
                $row = is_array($row) ? $this->normalizeCsvRow($row) : [(string) $row];
                fputcsv($FH, $row);
            }
            fclose($FH);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function index()
    {
        return view('back.item.bulk-upload');
    }
    public function export()
    {
        $lists = Item::where('item_type', '!=', 'affilite')->get();
        if ($lists->isEmpty()) {
            // Avoid fatal "Undefined array key 0" / empty header build.
            return $this->streamCsv([['message'], ['No products found']], 'products_csv_export.csv');
        }

        $cat = Category::whereIn('id', $lists->pluck('category_id')->filter()->unique()->values())
            ->pluck('name', 'id');
        $sub = Subcategory::whereIn('id', $lists->pluck('subcategory_id')->filter()->unique()->values())
            ->pluck('name', 'id');
        $child = ChieldCategory::whereIn('id', $lists->pluck('childcategory_id')->filter()->unique()->values())
            ->pluck('name', 'id');
        $brand = Brand::whereIn('id', $lists->pluck('brand_id')->filter()->unique()->values())
            ->pluck('name', 'id');

        $rows = [];
        foreach ($lists->toArray() as $list) {
            $list['photo'] = \App\Helpers\ImageHelper::storageImageUrl($list['photo']);
            $list['slug'] = Str::random(3) . $list['slug'] . Str::random(2);
            $list['category'] = (string) ($cat[$list['category_id']] ?? '');
            $list['subcategory'] = $list['subcategory_id'] ? (string) ($sub[$list['subcategory_id']] ?? '') : '';
            $list['childcategory'] = $list['childcategory_id'] ? (string) ($child[$list['childcategory_id']] ?? '') : '';
            $list['brand'] = $list['brand_id'] ? (string) ($brand[$list['brand_id']] ?? '') : '';
            unset($list['category_id'], $list['subcategory_id'], $list['childcategory_id'], $list['brand_id']);
            $rows[] = $list;
        }

        // add headers for each column in the CSV download
        array_unshift($rows, array_keys($rows[0]));

        return $this->streamCsv($rows, 'products_csv_export.csv');
    }

    /**
     * Download a "sample" CSV matching the current export format headers.
     * Useful for bulk imports: user fills rows under these headers.
     */
    public function sample()
    {
        // Build headers from current items table structure.
        $cols = Schema::getColumnListing('items');

        // In export() we replace these ids with readable names.
        $remove = ['category_id', 'subcategory_id', 'childcategory_id', 'brand_id'];
        $cols = array_values(array_filter($cols, function ($c) use ($remove) {
            return ! in_array($c, $remove, true);
        }));

        // Append export-only columns.
        foreach (['category', 'subcategory', 'childcategory', 'brand'] as $extra) {
            if (! in_array($extra, $cols, true)) {
                $cols[] = $extra;
            }
        }

        $rows = [];
        $rows[] = $cols;
        $rows[] = array_fill(0, count($cols), '');

        return $this->streamCsv($rows, 'products_csv_export.csv');
    }

    public function transactionExport()
    {
        $lists = Transaction::all()->toArray();
        if (empty($lists)) {
            return $this->streamCsv([['message'], ['No transactions found']], 'transaction_export.csv');
        }

        array_unshift($lists, array_keys($lists[0]));
        return $this->streamCsv($lists, 'transaction_export.csv');
    }

    public function orderExport()
    {
        $lists = Order::all()->toArray();
        if (empty($lists)) {
            return $this->streamCsv([['message'], ['No orders found']], 'order_csv_export.csv');
        }

        array_unshift($lists, array_keys($lists[0]));
        return $this->streamCsv($lists, 'order_csv_export.csv');
    }

    //*** POST Request
    public function import(Request $request)
    {

        try {
            // Bulk imports can take longer, especially on shared hosting.
            @set_time_limit(0);
            $filename = '';
            if ($file = $request->file('csv')) {
                $ext = strtolower((string) $file->getClientOriginalExtension());
                if ($ext !== 'csv') {
                    return back()->withError(__('Please upload a .csv file (not ') . $ext . ').');
                }
                $filename = time() . "." . $file->getClientOriginalExtension();
                $file->move('assets/temp_files', $filename);
            }

            $file = fopen('assets/temp_files/' . $filename, "r");

            $header = fgetcsv($file);
            if (! is_array($header) || $header === []) {
                fclose($file);
                return back()->withError(__('CSV header row missing.'));
            }

            // Normalize header keys (trim + lowercase)
            $keys = array_map(function ($h) {
                return strtolower(trim((string) $h));
            }, $header);

            // Cache lookups for speed (avoid N queries per row)
            $catMap = Category::pluck('id', 'name')->toArray();
            $subMap = Subcategory::pluck('id', 'name')->toArray();
            $childMap = ChieldCategory::pluck('id', 'name')->toArray();
            $brandMap = Brand::pluck('id', 'name')->toArray();

            $count = 0;
            while (($line = fgetcsv($file)) !== false) {
                if (! is_array($line) || $line === []) {
                    continue;
                }

                $row = [];
                foreach ($keys as $idx => $k) {
                    if ($k === '') continue;
                    $row[$k] = $line[$idx] ?? null;
                }

                // Skip completely empty/blank rows (common with Excel exports).
                $hasAnyValue = false;
                foreach ($row as $v) {
                    if (is_string($v)) {
                        if (trim($v) !== '') {
                            $hasAnyValue = true;
                            break;
                        }
                    } elseif ($v !== null && $v !== '') {
                        $hasAnyValue = true;
                        break;
                    }
                }
                if (! $hasAnyValue) {
                    continue;
                }

                // If there is no product name, treat as non-row.
                $rowName = trim((string) ($row['name'] ?? ''));
                if ($rowName === '') {
                    continue;
                }

                // Resolve category/subcategory/childcategory/brand by NAME columns (matching export/sample).
                $categoryName = trim((string) ($row['category'] ?? ''));
                $subcategoryName = trim((string) ($row['subcategory'] ?? ''));
                $childcategoryName = trim((string) ($row['childcategory'] ?? ''));
                $brandName = trim((string) ($row['brand'] ?? ''));

                $row['category_id'] = $categoryName !== '' && isset($catMap[$categoryName]) ? (int) $catMap[$categoryName] : 0;
                $row['subcategory_id'] = $subcategoryName !== '' && isset($subMap[$subcategoryName]) ? (int) $subMap[$subcategoryName] : 0;
                $row['childcategory_id'] = $childcategoryName !== '' && isset($childMap[$childcategoryName]) ? (int) $childMap[$childcategoryName] : 0;
                $row['brand_id'] = $brandName !== '' && isset($brandMap[$brandName]) ? (int) $brandMap[$brandName] : 0;

                unset($row['category'], $row['subcategory'], $row['childcategory'], $row['brand']);

                // Normalize JSON-array jewelry fields if provided as CSV/JSON in a cell.
                foreach (['metal_type', 'gold_karat', 'pdp_metal_variants'] as $jsonCol) {
                    if (! array_key_exists($jsonCol, $row)) continue;
                    $raw = $row[$jsonCol];
                    if ($raw === null || $raw === '') {
                        $row[$jsonCol] = null;
                        continue;
                    }
                    if (is_string($raw)) {
                        $decoded = json_decode($raw, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $row[$jsonCol] = $decoded;
                        }
                    }
                }

                // Photo: allow either a filename or a full URL (like export()).
                $photoCell = trim((string) ($row['photo'] ?? ''));
                if ($photoCell !== '') {
                    // Default: do not download remote images (can be slow/time out).
                    // If needed, pass ?download_images=1 in the URL to enable downloading.
                    $saved = $this->importPhotoCellToImages($photoCell, (string) $request->query('download_images') === '1');
                    if ($saved) {
                        $row['photo'] = $saved;
                        // keep thumbnail same (legacy behavior)
                        $row['thumbnail'] = $saved;
                    }
                }

                // Ensure slug exists to avoid admin "View" route errors.
                $slug = trim((string) ($row['slug'] ?? ''));
                if ($slug === '') {
                    $slug = Str::slug((string) ($row['name'] ?? 'item')) ?: ('item-' . Str::random(6));
                }
                // Keep slug unique
                $base = $slug;
                $n = 1;
                while (Item::where('slug', $slug)->exists()) {
                    $n++;
                    $slug = $base . '-' . $n;
                    if ($n > 50) {
                        $slug = $base . '-' . Str::random(6);
                        break;
                    }
                }
                $row['slug'] = $slug;

                // Always set default is_type if missing
                if (empty($row['is_type'])) {
                    $row['is_type'] = 'undefine';
                }

                // Create the item
                $data = new Item();
                $data->fill($row)->save();
                $count++;
            }
            fclose($file);

            $removefiles = glob('assets/temp_files/*');

            // Deleting all the files in the list
            foreach ($removefiles as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            return back()->withSuccess(__('Bulk Product File Imported Successfully.') . ' (' . $count . ')');
        } catch (\Throwable $th) {
            Log::error('CSV import failed', ['exception' => $th]);
            return back()->withError(__('Something is wrong!'));
        }
    }

    protected function importPhotoCellToImages(string $value, bool $downloadRemote = false): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/^https?:\\/\\//i', $value)) {
            $path = (string) parse_url($value, PHP_URL_PATH);
            $isLocalStorage = $path !== '' && stripos($path, '/storage/images/') !== false;

            // Optional: download third-party URLs and store a local filename (?download_images=1).
            if ($downloadRemote && ! $isLocalStorage) {
                try {
                    $ctx = stream_context_create([
                        'http' => ['timeout' => 6],
                        'https' => ['timeout' => 6],
                    ]);
                    $contents = @file_get_contents($value, false, $ctx);
                    if ($contents !== false && $contents !== '') {
                        $ext = 'jpg';
                        if (preg_match('/\\.(png|webp|jpg|jpeg)(\\?|#|$)/i', $value, $m)) {
                            $ext = strtolower($m[1] === 'jpeg' ? 'jpg' : $m[1]);
                        }
                        $name = ImageHelper::resolveAvailableFilename(
                            'images',
                            ImageHelper::filenameFromUrlOrPath($value)
                        );
                        Storage::put('images/' . $name, $contents);

                        return $name;
                    }
                } catch (\Throwable $e) {
                    // fall through to storing the original URL
                }
            }

            // Store full URL (matches bulk export and user CSV imports).
            return $value;
        }

        if (str_starts_with($value, '/')) {
            return url($value);
        }

        return $value;
    }


    public function uploadImage($file, $path, $delete = null)
    {
        return ImageHelper::ItemhandleUploadedImage($file, $path, $delete);
    }
}
