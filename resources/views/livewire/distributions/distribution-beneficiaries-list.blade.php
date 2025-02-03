<div>
    <header>
        <div class="mx-auto max-w-7xl  ">
          <h1 class="text-3xl text-main tracking-tight text-gray-900">{{Auth::user()->support()->distribution->title}}</h1>
        </div>
      </header>
      <div class="mt-8"></div>
    {{ $this->table }}
</div>
