<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$companies = App\Models\Company::all();
foreach($companies as $c) {
    if($c->user && $c->user->avatar) {
        $c->logo = $c->user->avatar;
        $c->save();
        echo "Updated company {$c->id} with logo {$c->logo}\n";
    }
}
echo "Done.\n";
