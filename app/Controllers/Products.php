<?php

namespace App\Controllers;

class Products extends BaseController
{
    public function index(): string
    {
        $db = db_connect();
        $query = trim((string) $this->request->getGet('q'));
        $categoryCode = trim((string) $this->request->getGet('category'));

        $builder = $db->table('products p')
            ->select('p.*, c.name AS category_name, c.code AS category_code')
            ->join('product_categories c', 'c.id = p.category_id')
            ->where('p.is_published', 1)
            ->orderBy('c.name', 'ASC')
            ->orderBy('p.name', 'ASC');

        if ($query !== '') {
            $builder->groupStart()->like('p.name', $query)->orLike('p.description', $query)->groupEnd();
        }
        if ($categoryCode !== '') {
            $builder->where('c.code', $categoryCode);
        }

        return view('products/index', [
            'products' => $builder->get()->getResultArray(),
            'categories' => $db->table('product_categories')->orderBy('name', 'ASC')->get()->getResultArray(),
            'query' => $query,
            'selectedCategory' => $categoryCode,
        ]);
    }

    public function show(string $slug): string
    {
        $db = db_connect();
        $product = $db->table('products p')
            ->select('p.*, c.name AS category_name, c.code AS category_code')
            ->join('product_categories c', 'c.id = p.category_id')
            ->where('p.slug', $slug)
            ->where('p.is_published', 1)
            ->get()
            ->getRowArray();

        if ($product === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $token = bin2hex(random_bytes(32));
        session()->set('product_inquiry_csrf_token', $token);

        return view('products/show', [
            'product' => $product,
            'specs' => $db->table('product_specs')->where('product_id', $product['id'])->orderBy('sort_order', 'ASC')->get()->getResultArray(),
            'images' => $db->table('product_images')->where('product_id', $product['id'])->orderBy('sort_order', 'ASC')->get()->getResultArray(),
            'inquiryToken' => $token,
        ]);
    }
}
