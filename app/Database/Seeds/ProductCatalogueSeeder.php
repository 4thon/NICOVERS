<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductCatalogueSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['panels', 'Solar Panel Systems', 'solar-panel-systems', 'Solar panel options for homes and businesses that want to generate clean electricity from available sunlight.', 'assets/images/solar-rooftop-sunrise.jpg', [['System role', 'Solar energy generation'], ['Suitable for', 'Residential and commercial projects']]],
            ['panels', 'Residential Solar Panel Array', 'residential-solar-panel-array', 'A panel-array planning example for households that want to begin a tailored solar assessment.', 'assets/images/solar-rooftop-sunrise.jpg', [['System role', 'Residential generation'], ['Planning stage', 'Site assessment required']]],
            ['panels', 'Commercial Solar Panel Array', 'commercial-solar-panel-array', 'A scalable panel-array example for commercial properties with broader energy requirements.', 'assets/images/solar-installation.jpg', [['System role', 'Commercial generation'], ['Planning stage', 'Energy review required']]],
            ['inverters', 'Solar Inverters', 'solar-inverters', 'Inverter equipment selected as part of a properly designed solar energy system.', 'assets/images/solar-installation.jpg', [['System role', 'Energy conversion'], ['Suitable for', 'Grid-connected solar projects']]],
            ['inverters', 'Hybrid Solar Inverter', 'hybrid-solar-inverter', 'A hybrid-inverter planning example for solar systems that may include battery storage.', 'assets/images/solar-technician.jpg', [['System role', 'Power conversion'], ['Compatible planning', 'Solar and storage consultation']]],
            ['inverters', 'String Solar Inverter', 'string-solar-inverter', 'A string-inverter planning example for correctly sized solar panel groups.', 'assets/images/solar-installation.jpg', [['System role', 'Power conversion'], ['Planning stage', 'System design required']]],
            ['batteries', 'Energy Storage Batteries', 'energy-storage-batteries', 'Battery storage options that help customers plan for energy resilience and changing household needs.', 'assets/images/solar-rooftop-sunrise.jpg', [['System role', 'Energy storage'], ['Suitable for', 'Solar systems with storage planning']]],
            ['batteries', 'Home Battery Storage', 'home-battery-storage', 'A home-storage planning example for customers exploring stored solar energy.', 'assets/images/solar-rooftop-sunrise.jpg', [['System role', 'Residential storage'], ['Planning stage', 'Load assessment required']]],
            ['batteries', 'Backup Battery Bank', 'backup-battery-bank', 'A battery-bank example for projects considering energy resilience and continuity.', 'assets/images/solar-technician.jpg', [['System role', 'Backup planning'], ['Planning stage', 'Site consultation required']]],
            ['mounting', 'Racking and Mounting Systems', 'racking-and-mounting-systems', 'Racking and mounting components that support a secure and site-appropriate solar panel installation.', 'assets/images/solar-installation.jpg', [['System role', 'Panel support'], ['Suitable for', 'Site-specific installations']]],
            ['mounting', 'Roof Mounting Rails', 'roof-mounting-rails', 'Mounting-rail components planned around roof type, panel layout, and installation conditions.', 'assets/images/solar-installation.jpg', [['System role', 'Roof support'], ['Planning stage', 'Roof review required']]],
            ['mounting', 'Ground Mounting Structure', 'ground-mounting-structure', 'A ground-mounting planning example for properties with suitable open space.', 'assets/images/solar-technician.jpg', [['System role', 'Ground support'], ['Planning stage', 'Site survey required']]],
            ['wires', 'Solar Wires and Cables', 'solar-wires-and-cables', 'Solar-ready wiring and cable components selected for the requirements of a complete installation.', 'assets/images/solar-technician.jpg', [['System role', 'System connection'], ['Suitable for', 'Complete solar installations']]],
            ['wires', 'Solar DC Cable Set', 'solar-dc-cable-set', 'DC cable components selected during full system design and installation planning.', 'assets/images/solar-technician.jpg', [['System role', 'DC connection'], ['Planning stage', 'System design required']]],
            ['wires', 'Solar Connection Kit', 'solar-connection-kit', 'Connection components considered as part of a safe, complete solar installation.', 'assets/images/solar-installation.jpg', [['System role', 'System connection'], ['Planning stage', 'Professional installation required']]],
        ];

        foreach ($products as [$categoryCode, $name, $slug, $description, $imagePath, $specs]) {
            $category = $this->db->table('product_categories')->where('code', $categoryCode)->get()->getRowArray();
            if ($category === null) {
                continue;
            }

            $existing = $this->db->table('products')->where('slug', $slug)->get()->getRowArray();
            $data = [
                'category_id' => $category['id'],
                'name' => $name,
                'slug' => $slug,
                'description' => $description,
                'image_path' => $imagePath,
                'is_published' => 1,
            ];

            if ($existing === null) {
                $this->db->table('products')->insert($data);
                $productId = (int) $this->db->insertID();
            } else {
                $productId = (int) $existing['id'];
                $this->db->table('products')->where('id', $productId)->update($data);
            }

            foreach ($specs as $order => [$specName, $specValue]) {
                $specExists = $this->db->table('product_specs')->where(['product_id' => $productId, 'spec_name' => $specName])->countAllResults() > 0;
                $specData = ['product_id' => $productId, 'spec_name' => $specName, 'spec_value' => $specValue, 'sort_order' => $order];
                if ($specExists) {
                    $this->db->table('product_specs')->where(['product_id' => $productId, 'spec_name' => $specName])->update($specData);
                } else {
                    $this->db->table('product_specs')->insert($specData);
                }
            }
        }
    }
}
