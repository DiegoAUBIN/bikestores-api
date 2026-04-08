# Script to download bike images from Unsplash

$imageDir = $PSScriptRoot
$brandsDir = Join-Path $imageDir "brands"
$categoriesDir = Join-Path $imageDir "categories"
$productsDir = Join-Path $imageDir "products"

# Brand images
$brandImages = @{
    "electra.jpg" = "https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=1200&q=80&dl=1"
    "haro.jpg" = "https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=1200&q=80&dl=1"
    "heller.jpg" = "https://images.unsplash.com/photo-1507035895480-2b3156c31fc8?auto=format&fit=crop&w=1200&q=80&dl=1"
    "pure-cycles.jpg" = "https://images.unsplash.com/photo-1516110833967-0b5716ca1387?auto=format&fit=crop&w=1200&q=80&dl=1"
    "ritchey.jpg" = "https://images.unsplash.com/photo-1519583272095-6433daf26b6e?auto=format&fit=crop&w=1200&q=80&dl=1"
    "strider.jpg" = "https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=1200&q=80&dl=1"
    "sun-bicycles.jpg" = "https://images.unsplash.com/photo-1471506480208-91b3a4cc78be?auto=format&fit=crop&w=1200&q=80&dl=1"
    "surly.jpg" = "https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=1200&q=80&dl=1"
    "trek.jpg" = "https://images.unsplash.com/photo-1516110833967-0b5716ca1387?auto=format&fit=crop&w=1200&q=80&dl=1"
}

# Category images
$categoryImages = @{
    "mountain.jpg" = "https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=1200&q=80&dl=1"
    "road.jpg" = "https://images.unsplash.com/photo-1516110833967-0b5716ca1387?auto=format&fit=crop&w=1200&q=80&dl=1"
    "electric.jpg" = "https://images.unsplash.com/photo-1571068316344-75bc76f77890?auto=format&fit=crop&w=1200&q=80&dl=1"
    "kids.jpg" = "https://images.unsplash.com/photo-1485965120184-e220f721d03e?auto=format&fit=crop&w=1200&q=80&dl=1"
    "city.jpg" = "https://images.unsplash.com/photo-1471506480208-91b3a4cc78be?auto=format&fit=crop&w=1200&q=80&dl=1"
}

# Product specific images
$productImages = @{
    "trek-820-2016.jpg" = "https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=1200&q=80&dl=1"
    "trek-fuel-ex-8-2016.jpg" = "https://images.unsplash.com/photo-1532298229144-0ec0c57515c7?auto=format&fit=crop&w=1200&q=80&dl=1"
}

# Hero strip background
$heroImage = @{
    "hero-bikes.jpg" = "https://images.unsplash.com/photo-1511994298241-608e28f14fde?auto=format&fit=crop&w=1800&q=80&dl=1"
}

Write-Host "⏳ Downloading brand images..." -ForegroundColor Cyan
foreach ($file in $brandImages.GetEnumerator()) {
    $path = Join-Path $brandsDir $file.Key
    try {
        Invoke-WebRequest -Uri $file.Value -OutFile $path -TimeoutSec 30
        Write-Host "✓ Downloaded $($file.Key)" -ForegroundColor Green
    } catch {
        Write-Host "✗ Failed to download $($file.Key): $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n⏳ Downloading category images..." -ForegroundColor Cyan
foreach ($file in $categoryImages.GetEnumerator()) {
    $path = Join-Path $categoriesDir $file.Key
    try {
        Invoke-WebRequest -Uri $file.Value -OutFile $path -TimeoutSec 30
        Write-Host "✓ Downloaded $($file.Key)" -ForegroundColor Green
    } catch {
        Write-Host "✗ Failed to download $($file.Key): $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n⏳ Downloading product images..." -ForegroundColor Cyan
foreach ($file in $productImages.GetEnumerator()) {
    $path = Join-Path $productsDir $file.Key
    try {
        Invoke-WebRequest -Uri $file.Value -OutFile $path -TimeoutSec 30
        Write-Host "✓ Downloaded $($file.Key)" -ForegroundColor Green
    } catch {
        Write-Host "✗ Failed to download $($file.Key): $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n⏳ Downloading hero background image..." -ForegroundColor Cyan
foreach ($file in $heroImage.GetEnumerator()) {
    $path = Join-Path $imageDir $file.Key
    try {
        Invoke-WebRequest -Uri $file.Value -OutFile $path -TimeoutSec 30
        Write-Host "✓ Downloaded $($file.Key)" -ForegroundColor Green
    } catch {
        Write-Host "✗ Failed to download $($file.Key): $($_.Exception.Message)" -ForegroundColor Red
    }
}

Write-Host "`n✅ Download complete!" -ForegroundColor Green
