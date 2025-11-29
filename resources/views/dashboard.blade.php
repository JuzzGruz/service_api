<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div>
                        <p><span id="local-time"></span></p>
                        <p><span id="local-date"></span></p>
                    </div>
                    <form action="{{ route('city') }}" method="post">
                        @csrf
                        <input 
                            type="text" 
                            id="city-input" 
                            name="coords_name" 
                            list="cities-list" 
                            placeholder="Введите город"
                            style="
                                background-color: #d3d3d3;  /* серый фон */
                                color: black;               /* текст черный */
                                width: 25%;                /* растянуть по ширине */
                                padding: 0.5rem;            /* внутренние отступы */
                                border: 1px solid #999;     /* рамка */
                                border-radius: 0.25rem;     /* скругление углов */
                                font-size: 16px;            /* размер текста */
                            "
                        >
                        <datalist id="cities-list">
                            @foreach ($data['cities'] as $city)
                                <option value="{{ $city['name'] }}" data-coords='{{ json_encode($city["coords"]) }}'></option>
                            @endforeach
                        </datalist>

                        <!-- Скрытое поле для координат -->
                        <input type="hidden" name="coords" id="coords-hidden">

                        <script>
                            document.getElementById('city-input').addEventListener('input', function() {
                                const val = this.value;
                                const list = document.getElementById('cities-list').children;
                                for (let i = 0; i < list.length; i++) {
                                    if (list[i].value === val) {
                                        document.getElementById('coords-hidden').value = list[i].dataset.coords;
                                        return;
                                    }
                                }
                                // Если совпадение не найдено — очищаем
                                document.getElementById('coords-hidden').value = '';
                            });
                        </script>

                        <button type="submit" style="margin-top: 0.5rem;">Выбрать</button>
                    </form>
                    <div class="weather">
                        @if ($data['weather']->cod != '200')
                            @if ($data['weather']->cod = '404')
                                <h2>Город не найден</h2>
                            @else
                                <h2>Ошибка</h2>
                            @endif
                        @else
                            <h2 class="weather__title">Погода в городе <?php echo $data['weather']->city; ?></h2>
                            <div class="weather__time">
                                <p class="weather__status"><?php echo ucwords($data['weather']->description); ?></p>
                            </div>
                            <div class="weather__forecast">
                                Температура: <span class="weather__min"><?php echo $data['weather']->temperature; ?>°C</span><br>
                                Ощущается как: <span class="weather__max"><?php echo $data['weather']->feels_like; ?>°C</span>
                            </div>
                            <p class="weather__humidity">Влажность: <?php echo $data['weather']->humidity; ?> %</p>
                            <p class="weather__wind">Ветер: <?php echo $data['weather']->wind_speed; ?> км/ч</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
