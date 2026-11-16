<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts::admin')]
class ProductForm extends Component
{
    use WithFileUploads;

    public int $currentStep = 1;

    public ?int $productId = null;

    // Step 1: Basics
    public string $name = '';

    public string $slug = '';

    public bool $slugManuallyEdited = false;

    public string $category = 'Dresses';

    public string $description = '';

    public string $status = 'published'; // 'published' | 'draft'

    public bool $featured = false;

    public bool $newest = false;

    // Step 2: Pricing & Commission
    public string $original_price = '';

    public int|string $discount_percent = 0;

    public string $price = '';

    public string $commission_type = 'percent';

    public int|string $commission_rate = 10;

    // Step 3: Sizes & Inventory
    public string $sizeMode = 'letter'; // 'letter', 'number', 'custom'

    public array $selectedSizes = ['S', 'M', 'L'];

    public array $sizeStocks = [
        'S' => 5,
        'M' => 10,
        'L' => 5,
    ];

    public int $quantity = 20;

    public string $customSizeInput = '';

    // Step 4: Media & Imagery
    public string $image = '';

    public $primaryUpload = null;

    public array $additionalImages = [];

    public $additionalUploads = [];

    public $photos = [];

    public string $newImageUrl = '';

    public bool $useUrlInput = false;

    public array $categories = ['Dresses', 'Tops', 'Bottoms', 'Outerwear', 'Sets', 'Accessories'];

    public array $sizePresets = [
        'letter' => [
            'label' => 'Letter sizes (XS, S, M, L, XL, 2XL)',
            'options' => ['XS', 'S', 'M', 'L', 'XL', '2XL', 'One size'],
        ],
        'number' => [
            'label' => 'Number sizes (4, 6, 8, 10, 12, 14, 16...)',
            'options' => ['4', '6', '8', '10', '12', '14', '16', '18', '20', '22'],
        ],
        'custom' => [
            'label' => 'Custom sizes',
            'options' => [],
        ],
    ];

    public function mount(?Product $product = null): void
    {
        if ($product?->exists) {
            $this->productId = $product->id;
            $this->name = $product->name;
            $this->slug = $product->slug;
            $this->slugManuallyEdited = true;
            $this->category = $product->category;
            $this->status = $product->status ?? 'published';
            $this->description = $product->description;
            $this->featured = (bool) $product->featured;
            $this->newest = (bool) $product->newest;

            // Pricing
            if ($product->original_price && $product->original_price > $product->price) {
                $this->original_price = (string) $product->original_price;
                $this->discount_percent = (int) round((($product->original_price - $product->price) / $product->original_price) * 100);
            } else {
                $this->original_price = (string) $product->price;
                $this->discount_percent = 0;
            }
            $this->computePrice();

            // Commission
            $this->commission_type = $product->commission_type ?? 'percent';
            $this->commission_rate = (int) ($product->commission_rate ?? 10);

            // Sizing
            $this->sizeMode = in_array($product->size_mode, ['letter', 'number', 'custom'], true) ? $product->size_mode : 'letter';
            $this->selectedSizes = ! empty($product->sizes) ? array_values($product->sizes) : ['S', 'M', 'L'];

            if (! empty($product->size_inventory) && is_array($product->size_inventory)) {
                $this->sizeStocks = $product->size_inventory;
                // Ensure all selected sizes have an entry
                foreach ($this->selectedSizes as $sz) {
                    if (! isset($this->sizeStocks[$sz])) {
                        $this->sizeStocks[$sz] = 5;
                    }
                }
            } else {
                $defaultPerSize = max(1, (int) round(($product->quantity ?? 15) / max(1, count($this->selectedSizes))));
                $this->sizeStocks = [];
                foreach ($this->selectedSizes as $sz) {
                    $this->sizeStocks[$sz] = $defaultPerSize;
                }
            }
            $this->syncTotalQuantity();

            // Media
            $this->image = $product->image;
            $gallery = $product->galleryImages();
            $this->additionalImages = array_values(array_filter(array_slice($gallery, 1)));
        } else {
            $this->computePrice();
            $this->syncTotalQuantity();
        }
    }

    // Step 1 handlers
    public function updatedName(): void
    {
        if (! $this->slugManuallyEdited || empty($this->slug)) {
            $this->generateSlug();
        }
    }

    public function updatedSlug(): void
    {
        $this->slugManuallyEdited = true;
    }

    public function generateSlug(): void
    {
        $this->slug = Str::slug(Str::ascii($this->name));
    }

    // Step 2 handlers: Pricing & Commission
    public function updatedPrice(): void
    {
        $selling = (int) $this->price;
        $orig = (int) $this->original_price;
        $disc = (int) $this->discount_percent;

        if ($orig > $selling && $selling > 0) {
            $this->discount_percent = (int) round((($orig - $selling) / $orig) * 100);
        } elseif ($disc === 0 || $orig < $selling) {
            $this->original_price = $this->price;
            $this->discount_percent = 0;
        }
    }

    public function updatedOriginalPrice(): void
    {
        $orig = (int) $this->original_price;
        $selling = (int) $this->price;
        $disc = (int) $this->discount_percent;

        if ($orig > 0 && $disc > 0) {
            $this->price = (string) max(100, (int) round($orig * (1 - ($disc / 100))));
        } elseif ($orig > 0 && $selling > 0 && $orig > $selling) {
            $this->discount_percent = (int) round((($orig - $selling) / $orig) * 100);
        } elseif (empty($this->price) && $orig > 0) {
            $this->price = (string) $orig;
        }
    }

    public function setDiscount(int $percent): void
    {
        $this->discount_percent = max(0, min(95, $percent));

        $orig = (int) $this->original_price;
        $selling = (int) $this->price;

        if ($this->discount_percent === 0) {
            if ($orig > 0) {
                $this->price = (string) $orig;
            } elseif ($selling > 0) {
                $this->original_price = (string) $selling;
            }

            return;
        }

        if ($orig <= 0 && $selling > 0) {
            $orig = (int) round($selling / (1 - ($this->discount_percent / 100)));
            $this->original_price = (string) $orig;
        } elseif ($orig > 0) {
            $this->price = (string) max(100, (int) round($orig * (1 - ($this->discount_percent / 100))));
        }
    }

    public function updatedDiscountPercent(): void
    {
        $this->discount_percent = max(0, min(95, (int) $this->discount_percent));
        $this->computePrice();
    }

    public function computePrice(): void
    {
        $orig = (int) $this->original_price;
        $disc = (int) $this->discount_percent;

        if ($orig <= 0) {
            if ((int) $this->price > 0) {
                $this->original_price = $this->price;
                $orig = (int) $this->original_price;
            } else {
                return;
            }
        }

        if ($disc > 0) {
            $calc = (int) round($orig * (1 - ($disc / 100)));
            $this->price = (string) max(100, $calc);
        } else {
            $this->price = (string) $orig;
        }
    }

    public function updatedQuantity(): void
    {
        $count = count($this->selectedSizes);
        if ($count > 0 && $this->quantity >= 0) {
            $base = intdiv((int) $this->quantity, $count);
            $rem = (int) $this->quantity % $count;
            $this->sizeStocks = [];
            foreach ($this->selectedSizes as $i => $sz) {
                $this->sizeStocks[$sz] = $base + ($i < $rem ? 1 : 0);
            }
        }
    }

    public function getCalculatedSavingsProperty(): int
    {
        $orig = (int) $this->original_price;
        $selling = (int) $this->price;

        return max(0, $orig - $selling);
    }

    public function getCalculatedCommissionProperty(): int
    {
        $price = (int) $this->price;
        $rate = (int) ($this->commission_rate ?: 10);
        if ($this->commission_type === 'fixed') {
            return (int) min($rate, $price);
        }

        return (int) round(($price * $rate) / 100);
    }

    // Step 3 handlers: Sizing & Stock
    public function setSizeMode(string $mode): void
    {
        if (array_key_exists($mode, $this->sizePresets)) {
            $this->sizeMode = $mode;
        }
    }

    public function toggleSize(string $size): void
    {
        if (in_array($size, $this->selectedSizes, true)) {
            $this->selectedSizes = array_values(array_diff($this->selectedSizes, [$size]));
            unset($this->sizeStocks[$size]);
        } else {
            $this->selectedSizes[] = $size;
            $this->sizeStocks[$size] = $this->sizeStocks[$size] ?? 5;
        }
        $this->syncTotalQuantity();
    }

    public function selectPresetSizes(): void
    {
        $options = $this->sizePresets[$this->sizeMode]['options'] ?? [];
        foreach ($options as $opt) {
            if (! in_array($opt, $this->selectedSizes, true)) {
                $this->selectedSizes[] = $opt;
                $this->sizeStocks[$opt] = $this->sizeStocks[$opt] ?? 5;
            }
        }
        $this->syncTotalQuantity();
    }

    public function clearSizes(): void
    {
        $this->selectedSizes = [];
        $this->sizeStocks = [];
        $this->quantity = 0;
    }

    public function addCustomSize(): void
    {
        $clean = trim($this->customSizeInput);
        if ($clean !== '' && ! in_array($clean, $this->selectedSizes, true)) {
            $this->selectedSizes[] = $clean;
            $this->sizeStocks[$clean] = 5;
            $this->syncTotalQuantity();
        }
        $this->customSizeInput = '';
    }

    public function updateSizeStock(string $size, int $amount): void
    {
        $this->sizeStocks[$size] = max(0, $amount);
        $this->syncTotalQuantity();
    }

    public function updatedSizeStocks(): void
    {
        $this->syncTotalQuantity();
    }

    public function syncTotalQuantity(): void
    {
        $total = 0;
        foreach ($this->selectedSizes as $sz) {
            $total += max(0, (int) ($this->sizeStocks[$sz] ?? 0));
        }
        $this->quantity = $total;
    }

    // Step 4 handlers: Media
    public function updatedPhotos(): void
    {
        $this->validate([
            'photos.*' => 'required|file|image|max:102400',
        ], [
            'photos.*.image' => 'Each file must be an image (JPEG, PNG, WEBP, AVIF, GIF).',
            'photos.*.max' => 'Each image size may not exceed 100MB.',
        ]);

        $this->storePhotos();
    }

    public function storePhotos(): void
    {
        if (empty($this->photos)) {
            return;
        }

        $folder = public_path('uploads/products');
        if (! file_exists($folder)) {
            File::makeDirectory($folder, 0755, true, true);
        }

        foreach ($this->photos as $file) {
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $safeName = Str::slug(Str::ascii($this->name ?: 'piece')) ?: 'piece';
            $filename = $safeName . '-' . Str::random(8) . '.' . strtolower($extension);
            $target = $folder . '/' . $filename;

            $sourcePath = method_exists($file, 'getRealPath') && file_exists($file->getRealPath())
                ? $file->getRealPath()
                : $file->path();

            File::copy($sourcePath, $target);
            @chmod($target, 0664);

            try {
                if (method_exists($file, 'delete')) {
                    $file->delete();
                }
            } catch (\Throwable $e) {
                // ignore temp cleanup error
            }

            $url = '/uploads/products/' . $filename;

            if (empty($this->image)) {
                $this->image = $url;
            } elseif (! in_array($url, $this->additionalImages, true)) {
                $this->additionalImages[] = $url;
            }
        }

        $this->photos = [];
        $this->dispatch('toast', message: 'Pictures uploaded successfully.');
    }

    public function setFeaturedImage(string $url): void
    {
        if ($this->image === $url) {
            return;
        }

        $all = array_values(array_unique(array_filter(array_merge([$this->image], $this->additionalImages))));
        if (in_array($url, $all, true)) {
            $this->image = $url;
            $this->additionalImages = array_values(array_filter($all, fn ($img) => $img !== $url));
            $this->dispatch('toast', message: 'Featured image updated.');
        }
    }

    public function removeImage(string $url): void
    {
        if ($this->image === $url) {
            $this->image = array_shift($this->additionalImages) ?? '';
        } else {
            $this->additionalImages = array_values(array_filter($this->additionalImages, fn ($img) => $img !== $url));
        }

        $this->dispatch('toast', message: 'Picture removed.');
    }

    public function addImageUrl(): void
    {
        $clean = trim($this->newImageUrl);
        if ($clean !== '') {
            $clean = Product::normalizeImageUrl($clean);
            if (empty($this->image)) {
                $this->image = $clean;
            } elseif ($clean !== $this->image && ! in_array($clean, $this->additionalImages, true)) {
                $this->additionalImages[] = $clean;
            }
            $this->newImageUrl = '';
            $this->dispatch('toast', message: 'Picture URL added.');
        }
    }

    public function updatedPrimaryUpload(): void
    {
        $this->validate([
            'primaryUpload' => 'required|file|image|max:102400',
        ], [
            'primaryUpload.image' => 'The file must be an image (JPEG, PNG, WEBP, AVIF, GIF).',
            'primaryUpload.max' => 'The image size may not exceed 100MB.',
        ]);

        $this->storePrimaryUpload();
    }

    public function storePrimaryUpload(): void
    {
        if (! $this->primaryUpload) {
            return;
        }

        $folder = public_path('uploads/products');
        if (! file_exists($folder)) {
            File::makeDirectory($folder, 0755, true, true);
        }

        $extension = $this->primaryUpload->getClientOriginalExtension() ?: 'jpg';
        $safeName = Str::slug(Str::ascii($this->name ?: 'piece')) ?: 'piece';
        $filename = $safeName . '-' . Str::random(8) . '.' . strtolower($extension);
        $target = $folder . '/' . $filename;

        $sourcePath = method_exists($this->primaryUpload, 'getRealPath') && file_exists($this->primaryUpload->getRealPath())
            ? $this->primaryUpload->getRealPath()
            : $this->primaryUpload->path();

        File::copy($sourcePath, $target);
        @chmod($target, 0664);

        try {
            if (method_exists($this->primaryUpload, 'delete')) {
                $this->primaryUpload->delete();
            }
        } catch (\Throwable $e) {
            // ignore temp cleanup error
        }

        $this->image = '/uploads/products/' . $filename;
        $this->primaryUpload = null;
    }

    public function removePrimaryImage(): void
    {
        $this->image = '';
        $this->primaryUpload = null;
    }

    public function updatedAdditionalUploads(): void
    {
        $this->validate([
            'additionalUploads.*' => 'required|file|image|max:102400',
        ], [
            'additionalUploads.*.image' => 'Each file must be an image (JPEG, PNG, WEBP, AVIF, GIF).',
            'additionalUploads.*.max' => 'Each image size may not exceed 100MB.',
        ]);

        $this->storeAdditionalUploads();
    }

    public function storeAdditionalUploads(): void
    {
        if (empty($this->additionalUploads)) {
            return;
        }

        $folder = public_path('uploads/products');
        if (! file_exists($folder)) {
            File::makeDirectory($folder, 0755, true, true);
        }

        foreach ($this->additionalUploads as $file) {
            $extension = $file->getClientOriginalExtension() ?: 'jpg';
            $safeName = Str::slug(Str::ascii($this->name ?: 'piece')) ?: 'piece';
            $filename = $safeName . '-angle-' . Str::random(8) . '.' . strtolower($extension);
            $target = $folder . '/' . $filename;

            $sourcePath = method_exists($file, 'getRealPath') && file_exists($file->getRealPath())
                ? $file->getRealPath()
                : $file->path();

            File::copy($sourcePath, $target);
            @chmod($target, 0664);

            try {
                if (method_exists($file, 'delete')) {
                    $file->delete();
                }
            } catch (\Throwable $e) {
                // ignore temp cleanup error
            }

            $this->additionalImages[] = '/uploads/products/' . $filename;
        }

        $this->additionalUploads = [];
    }

    public function addAdditionalImage(): void
    {
        $clean = trim($this->newImageUrl);
        if ($clean !== '') {
            $clean = Product::normalizeImageUrl($clean);
            if (! in_array($clean, $this->additionalImages, true) && $clean !== $this->image) {
                $this->additionalImages[] = $clean;
            }
            $this->newImageUrl = '';
        }
    }

    public function removeAdditionalImage(int $index): void
    {
        unset($this->additionalImages[$index]);
        $this->additionalImages = array_values($this->additionalImages);
    }

    public function makePrimaryImage(int $index): void
    {
        if (isset($this->additionalImages[$index])) {
            $oldPrimary = $this->image;
            $this->image = $this->additionalImages[$index];
            $this->additionalImages[$index] = $oldPrimary;
            $this->additionalImages = array_values(array_filter($this->additionalImages));
        }
    }

    // Wizard Step Navigation
    public function goToStep(int $step): void
    {
        if ($step < 1 || $step > 5) {
            return;
        }

        if ($step > $this->currentStep) {
            // Validate intermediate steps before jumping forward
            for ($s = $this->currentStep; $s < $step; $s++) {
                if (! $this->validateStep($s)) {
                    return;
                }
            }
        }

        $this->currentStep = $step;
    }

    public function nextStep(): void
    {
        if ($this->validateStep($this->currentStep)) {
            $this->currentStep = min(5, $this->currentStep + 1);
        }
    }

    public function prevStep(): void
    {
        $this->currentStep = max(1, $this->currentStep - 1);
    }

    public function updatedSelectedSizes(): void
    {
        $count = count($this->selectedSizes);
        if ($count > 0 && $this->quantity > 0) {
            $base = intdiv((int) $this->quantity, $count);
            $rem = (int) $this->quantity % $count;
            $this->sizeStocks = [];
            foreach ($this->selectedSizes as $i => $sz) {
                $this->sizeStocks[$sz] = $base + ($i < $rem ? 1 : 0);
            }
        } else {
            foreach ($this->selectedSizes as $sz) {
                if (! isset($this->sizeStocks[$sz])) {
                    $this->sizeStocks[$sz] = 5;
                }
            }
            $this->syncTotalQuantity();
        }
    }

    public function validateStep(int $step): bool
    {
        $this->slug = Str::slug(Str::ascii($this->slug ?: $this->name));

        if ($step === 1) {
            $this->validate([
                'name' => 'required|string|max:160',
                'slug' => ['required', 'string', 'max:180', Rule::unique('products', 'slug')->ignore($this->productId)],
                'category' => 'required|in:Dresses,Tops,Bottoms,Outerwear,Sets,Accessories',
                'description' => 'required|string|min:15|max:2000',
                'status' => 'required|in:published,draft',
            ], [
                'name.required' => 'Please enter the piece title.',
                'slug.required' => 'A URL slug is required.',
                'slug.unique' => 'This URL slug is already taken. Please change the title or edit the slug.',
                'category.required' => 'Please select a collection category.',
                'description.required' => 'Please provide a story & fabric description.',
                'description.min' => 'The description must be at least 15 characters long.',
            ]);
        } elseif ($step === 2) {
            if (empty($this->original_price) && (int) $this->price > 0) {
                $this->original_price = $this->price;
            }
            if (empty($this->price) && (int) $this->original_price > 0) {
                $this->price = $this->original_price;
            }
            $this->validate([
                'price' => 'required|integer|min:100',
                'original_price' => 'nullable|integer|min:100',
                'discount_percent' => 'nullable|integer|min:0|max:95',
                'commission_type' => 'required|in:percent,percentage,fixed',
                'commission_rate' => 'required|integer|min:1',
            ], [
                'price.required' => 'Please enter the retail selling price.',
                'price.min' => 'The selling price must be at least ₦100.',
                'original_price.min' => 'The compare-at price must be at least ₦100.',
                'commission_rate.required' => 'The commission rate is required.',
                'commission_rate.min' => 'The commission rate must be at least 1.',
            ]);
        } elseif ($step === 3) {
            foreach ($this->selectedSizes as $sz) {
                if (! isset($this->sizeStocks[$sz])) {
                    $this->sizeStocks[$sz] = 5;
                }
            }
            $this->syncTotalQuantity();
            $this->validate([
                'sizeMode' => 'required|string|in:letter,number,custom',
                'selectedSizes' => 'required|array|min:1',
                'sizeStocks' => 'required|array',
            ], [
                'selectedSizes.required' => 'Please select at least one size for this piece.',
                'selectedSizes.min' => 'Please select at least one size for this piece.',
            ]);
        } elseif ($step === 4) {
            $this->storePhotos();
            $this->storePrimaryUpload();
            $this->validate([
                'image' => 'required|string|max:600',
            ], [
                'image.required' => 'Please upload or select at least one photo for this piece.',
            ]);
        }

        return true;
    }

    public function save(?string $overrideStatus = null)
    {
        if ($overrideStatus && in_array($overrideStatus, ['published', 'draft'], true)) {
            $this->status = $overrideStatus;
        }

        $this->storePhotos();
        $this->storePrimaryUpload();
        $this->storeAdditionalUploads();

        try {
            // Validate all steps
            $this->validateStep(1);
            $this->validateStep(2);
            $this->validateStep(3);
            if ($this->status !== 'draft') {
                $this->validateStep(4);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            $this->dispatch('toast', message: 'Cannot save: ' . $firstError);
            $this->dispatch('form-error', message: $firstError);
            throw $e;
        }

        $normalizedPrimary = Product::normalizeImageUrl($this->image ?: asset('assets/brand-guide.png'));
        $allImages = array_values(array_unique(array_filter(array_merge([$normalizedPrimary], $this->additionalImages))));

        $cleanSizeInventory = [];
        foreach ($this->selectedSizes as $sz) {
            $cleanSizeInventory[$sz] = max(0, (int) ($this->sizeStocks[$sz] ?? 0));
        }

        $orig = (int) $this->original_price;
        $selling = (int) $this->price;

        $totalQty = ! empty($cleanSizeInventory) ? array_sum($cleanSizeInventory) : (int) $this->quantity;

        $payload = [
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'status' => $this->status,
            'price' => $selling,
            'original_price' => $orig > $selling ? $orig : ($this->discount_percent > 0 ? $orig : null),
            'quantity' => $totalQty,
            'size_inventory' => $cleanSizeInventory,
            'commission_type' => $this->commission_type,
            'commission_rate' => (int) ($this->commission_rate ?: 10),
            'size_mode' => $this->sizeMode,
            'sizes' => array_values($this->selectedSizes),
            'description' => $this->description,
            'image' => $normalizedPrimary,
            'images' => $allImages,
            'featured' => $this->featured,
            'newest' => $this->newest,
        ];

        try {
            if ($this->productId) {
                Product::query()->findOrFail($this->productId)->update($payload);
                session()->flash('admin_toast', "Piece updated as {$this->status}.");
            } else {
                Product::query()->create($payload);
                session()->flash('admin_toast', "Piece added to collection as {$this->status}.");
            }
        } catch (\Throwable $e) {
            $this->dispatch('toast', message: 'Failed to save piece: ' . $e->getMessage());
            $this->addError('save_error', 'Database error: ' . $e->getMessage());
            return;
        }

        return $this->redirectRoute('admin.products', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.product-form')
            ->title($this->productId ? 'Edit piece — Dharmie' : 'New piece — Dharmie');
    }
}
