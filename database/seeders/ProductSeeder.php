<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        for ($i=0; $i < 10; $i++) { 
            DB::table('products')->insert([
                "name" => "Off ".$i."White Cotton Bomber the quick thing",
                "slug" => "off-".$i."-white-cotton-bomber-the-quick-thing",
                "sku" => "666p".$i,
                "description" => '<p>Short Hooded Coat features a straight body, large pockets with button flaps, ventilation air holes, and a string detail along the hemline. The style is completed with a drawstring hood, featuring Rains’ signature built-in cap. Made from waterproof, matte PU, this lightweight unisex rain jacket is an ode to nostalgia through its classic silhouette and utilitarian design details.</p>p>- Casual unisex fit</p>p>- 64% polyester, 36% polyurethane</p><p>- Water column pressure: 4000 mm</p><p>- Model is 187cm tall and wearing a size S / M</p><p>- Unisex fit</p><p>- Drawstring hood with built-in cap</p><p>- Front placket with snap buttons</p><p>- Ventilation under armpit</p><p>- Adjustable cuffs</p><p>- Double welted front pockets</p><p>- Adjustable elastic string at hempen</p><p>- Ultrasonically welded seams</p><p>This is a unisex item, please check our clothing &amp; footwear sizing guide for specific Rains jacket sizing information. RAINS comes from the rainy nation of Denmark at the edge of the European continent, close to the ocean and with prevailing westerly winds; all factors that contribute to an average of 121 rain days each year. Arising from these rainy weather conditions comes the attitude that a quick rain shower may be beautiful, as well as moody- but first and foremost requires the right outfit. Rains focus on the whole experience of going outside on rainy days, issuing an invitation to explore even in the most mercurial weather.</p>',
                "image" => "default.jpg",
                "gallery" => '["default.jpg","default.jpg","default.jpg","default.jpg"]',
                "price" => 666+$i,
                "quantity" => 6*$i,
                "category_id" => 1,
                "stock_status" => "in_stock",
                "status" => ($i%2 == 1)?"published":"draft",
                "type" => "internal",
                "weight" => $i,
                "length" => 2,
                "width" => 7,
                "height" => 5,
            ]);
        }
    }
}
