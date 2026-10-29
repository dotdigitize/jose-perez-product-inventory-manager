<?php
namespace App\Services;

use Illuminate\Support\Str;
use RuntimeException;

class ProductStore
{
    private string $path;
    public function __construct() { $this->path = storage_path('app/private/products.json'); }
    public function all(): array { return $this->locked(function ($records) { usort($records, fn ($a, $b) => strcmp($b['submitted_at'], $a['submitted_at'])); return $records; }, false); }
    public function create(array $data): array { return $this->locked(function (&$records) use ($data) { $item = ['id' => (string) Str::uuid(), 'name' => $data['name'], 'quantity' => (int) $data['quantity'], 'price' => number_format((float) $data['price'], 2, '.', ''), 'submitted_at' => now()->utc()->toISOString()]; $records[] = $item; return $item; }); }
    public function update(string $id, array $data): ?array { return $this->locked(function (&$records) use ($id, $data) { foreach ($records as &$item) if ($item['id'] === $id) { $item['name']=$data['name']; $item['quantity']=(int)$data['quantity']; $item['price']=number_format((float)$data['price'],2,'.',''); return $item; } return null; }); }
    private function locked(callable $callback, bool $write = true): mixed { $handle=fopen($this->path.'.lock','c+'); if (!$handle || !flock($handle, LOCK_EX)) throw new RuntimeException('Unable to lock product storage.'); try { $records=is_file($this->path)?json_decode(file_get_contents($this->path),true,512,JSON_THROW_ON_ERROR):[]; $result=$callback($records); if($write){$json=json_encode($records,JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_THROW_ON_ERROR)."\n";$tmp=tempnam(dirname($this->path),'products-');file_put_contents($tmp,$json,LOCK_EX);chmod($tmp,0640);rename($tmp,$this->path);} return $result; } finally { flock($handle, LOCK_UN); fclose($handle); } }
}
