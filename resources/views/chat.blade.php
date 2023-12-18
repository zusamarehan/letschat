<!DOCTYPE html>
<html class="h-full bg-white" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        @vite('resources/css/app.css')
        <title>LetsChat</title>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        <script>
            let echo_service;

            window.onload = function () {
                echo_service = new WebSocket('ws://127.0.0.1:9509?userdata={{auth()->user()->id}}');
                echo_service.onmessage = function (event) {
                    Alpine.store('inbox').append(JSON.parse(event.data))
                }
                echo_service.onopen = function () {
                    console.log('Connected to WebSocket!');
                    // append("Connected to WebSocket!");
                }
                echo_service.onclose = function () {
                    console.log('Closed to WebSocket!');
                    // append("Connection closed");
                }
                echo_service.onerror = function () {
                    console.log('Error to WebSocket!');
                    // append("Error happens");
                }
            }

            document.addEventListener('alpine:init', () => {
                Alpine.store('inbox', {
                    on: false,
                    id: null,
                    new: [],
                    username: null,
                    message: '',
                    messages: [],
                    async show(id, username) {
                        await fetch(`/connection/messages?connection_id=${id}`)
                            .then(function (response) {
                                if (!response.ok) {
                                    throw new Error(`Network response was not ok: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                this.messages = data;
                            }),
                        this.username = username
                        this.id = id
                        this.on = true

                        // Check if the value exists in the array
                        let index = this.new.indexOf(this.id * 1);

                        // If the value exists, remove it
                        if (index !== -1) {
                            this.new.splice(index, 1);
                        }
                    },
                    close() {
                        this.on = false
                    },
                    sendMessage() {
                        echo_service.send(JSON.stringify(
                            {
                                'msg' : this.message,
                                'intendedTo': this.id,
                            }
                        ));
                        this.message = ''
                    },
                    append(msg) {
                        this.messages.push(msg.data)
                        console.log(msg.sender *1, this.id * 1)
                        if(msg.new === true && ! this.new.includes(msg.sender * 1) && this. id * 1 !== msg.sender * 1) {
                            this.new.push(msg.sender * 1)
                            console.log(this.new)
                        }
                        document.getElementById('inbox-view').scrollTo(0, 1999999)
                    }
                })
            })

        </script>
    </head>
    <body class="h-full">

    <!-- component -->
    <main class="flex w-full h-full shadow-lg rounded-3xl" x-data>
{{--        Maybe Later --}}
{{--        <section class="flex flex-col w-1/12 bg-white rounded-l-3xl">--}}
{{--            <div class="w-16 mx-auto mt-12 mb-10 p-4 bg-indigo-600 rounded-2xl text-white">--}}
{{--                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"--}}
{{--                          d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76" />--}}
{{--                </svg>--}}
{{--            </div>--}}
{{--            <nav class="relative flex flex-col items-center">--}}
{{--                <a href="#" class="relative w-16 p-4 bg-purple-100 text-purple-900 rounded-2xl mb-4">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"--}}
{{--                              d="M8 4H6a2 2 0 00-2 2v12a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-2m-4-1v8m0 0l3-3m-3 3L9 8m-5 5h2.586a1 1 0 01.707.293l2.414 2.414a1 1 0 00.707.293h3.172a1 1 0 00.707-.293l2.414-2.414a1 1 0 01.707-.293H20" />--}}
{{--                    </svg>--}}
{{--                    <span--}}
{{--                        class="absolute -top-2 -right-2 bg-red-600 h-6 w-6 p-2 flex justify-center items-center text-white rounded-full">3</span>--}}
{{--                </a>--}}
{{--                <a href="#" class="w-16 p-4 border text-gray-700 rounded-2xl mb-4">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"--}}
{{--                              d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />--}}
{{--                    </svg>--}}
{{--                </a>--}}
{{--                <a href="#" class="w-16 p-4 border text-gray-700 rounded-2xl mb-4">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"--}}
{{--                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />--}}
{{--                    </svg>--}}
{{--                </a>--}}
{{--                <a href="#" class="w-16 p-4 border text-gray-700 rounded-2xl mb-4">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"--}}
{{--                              d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />--}}
{{--                    </svg>--}}
{{--                </a>--}}
{{--                <a href="#" class="w-16 p-4 border text-gray-700 rounded-2xl mb-24">--}}
{{--                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"--}}
{{--                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />--}}
{{--                    </svg>--}}
{{--                </a>--}}
{{--            </nav>--}}
{{--        </section>--}}
        <section class="flex flex-col pt-12 w-4/12 bg-gray-50 h-full overflow-y-scroll">
            Welcome {{ auth()->user()->username }}
            <form action="{{route('connection.add')}}" method="POST">
                    <div class="flex">
                        <div class="px-4 w-full">
                            @csrf
                            <label>
                                <input name="search" class="rounded-lg p-4 bg-gray-100 transition duration-200 focus:outline-none focus:ring-2 w-full"
                                       placeholder="Search..." />
                            </label>
                            @if($errors->any())
                                <span class="text-red-400">{{ implode('', $errors->all(':message')) }}</span>
                            @endif
                            @if (\Session::has('added'))
                                <div class="alert alert-success text-green-400">
                                    <ul>
                                        <li>{!! \Session::get('added') !!}</li>
                                    </ul>
                                </div>
                            @endif
                        </div>


                        <div class="pr-4">
                            <button class="w-16 p-4 border text-gray-700 rounded-2xl">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>

                            </button>
                        </div>
                    </div>

                </form>

            <ul class="mt-6 select-none">
                @forelse($connections as $connection)
                    <li class="py-5 border-b px-3 transition hover:bg-indigo-100 cursor-pointer" @click="$store.inbox.show('{{$connection->id}}', '{{$connection->username}}')">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold">{{ $connection->username }}</h3>
                            <template x-if="($store.inbox.new).includes({{$connection->id }})">
                                <div class="-top-2 -right-2 bg-green-600 h-6 w-6 p-2 flex justify-center items-center text-white rounded-full"></div>
                            </template>
                            {{--    <p class="text-md text-gray-400">23m ago</p> --}}
                        </div>
                        {{--   <div class="text-md italic text-gray-400">You have been invited!</div> --}}
                    </li>
                @empty
                    <li class="py-5 border-b px-3 transition hover:bg-indigo-100">
                        <a href="#" class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold">No connections</h3>
                        </a>
                    </li>
                @endforelse
            </ul>
        </section>
        <div class="w-2/3 border flex flex-col" x-show="$store.inbox.on">

            <!-- Header -->
            <div class="py-2 px-3 bg-grey-lighter flex flex-row justify-between items-center">
                <div class="flex items-center">
                    <div>
                        <img class="w-10 h-10 rounded-full" src="https://darrenjameseeley.files.wordpress.com/2014/09/expendables3.jpeg"/>
                    </div>
                    <div class="ml-4">
                        <p class="text-grey-darkest">
                            <span x-html="$store.inbox.username"></span>
                        </p>
                    </div>
                </div>

                <div class="flex">
                    <div>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="#263238" fill-opacity=".5" d="M15.9 14.3H15l-.3-.3c1-1.1 1.6-2.7 1.6-4.3 0-3.7-3-6.7-6.7-6.7S3 6 3 9.7s3 6.7 6.7 6.7c1.6 0 3.2-.6 4.3-1.6l.3.3v.8l5.1 5.1 1.5-1.5-5-5.2zm-6.2 0c-2.6 0-4.6-2.1-4.6-4.6s2.1-4.6 4.6-4.6 4.6 2.1 4.6 4.6-2 4.6-4.6 4.6z"></path></svg>
                    </div>
                    <div class="ml-6">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="#263238" fill-opacity=".5" d="M1.816 15.556v.002c0 1.502.584 2.912 1.646 3.972s2.472 1.647 3.974 1.647a5.58 5.58 0 0 0 3.972-1.645l9.547-9.548c.769-.768 1.147-1.767 1.058-2.817-.079-.968-.548-1.927-1.319-2.698-1.594-1.592-4.068-1.711-5.517-.262l-7.916 7.915c-.881.881-.792 2.25.214 3.261.959.958 2.423 1.053 3.263.215l5.511-5.512c.28-.28.267-.722.053-.936l-.244-.244c-.191-.191-.567-.349-.957.04l-5.506 5.506c-.18.18-.635.127-.976-.214-.098-.097-.576-.613-.213-.973l7.915-7.917c.818-.817 2.267-.699 3.23.262.5.501.802 1.1.849 1.685.051.573-.156 1.111-.589 1.543l-9.547 9.549a3.97 3.97 0 0 1-2.829 1.171 3.975 3.975 0 0 1-2.83-1.173 3.973 3.973 0 0 1-1.172-2.828c0-1.071.415-2.076 1.172-2.83l7.209-7.211c.157-.157.264-.579.028-.814L11.5 4.36a.572.572 0 0 0-.834.018l-7.205 7.207a5.577 5.577 0 0 0-1.645 3.971z"></path></svg>
                    </div>
                    <div class="ml-6">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="#263238" fill-opacity=".6" d="M12 7a2 2 0 1 0-.001-4.001A2 2 0 0 0 12 7zm0 2a2 2 0 1 0-.001 3.999A2 2 0 0 0 12 9zm0 6a2 2 0 1 0-.001 3.999A2 2 0 0 0 12 15z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <div id="inbox-view" class="flex-1 overflow-auto" style="background-color: #DAD3CC">
                <div class="py-2 px-3" id="chat-logs">

{{--                    <div class="flex justify-center mb-2">--}}
{{--                        <div class="rounded py-2 px-4" style="background-color: #DDECF2">--}}
{{--                            <p class="text-sm uppercase">--}}
{{--                                February 20, 2018--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <template x-for="chat in $store.inbox.messages">
                        <div :class="chat.sender_id == '{{auth()->user()->id}}' ? 'flex  mb-2 justify-end' : 'flex  mb-2' ">
                            <div class="rounded py-2 px-3" style="background-color: #E2F7CB">
                                <p class="text-sm mt-1" x-text="chat.message"></p>
                                <p class="text-right text-xs text-grey-dark mt-1" x-text="chat.created_at"></p>
                            </div>
                        </div>
                    </template>

                </div>
            </div>

            <!-- Input -->
            <div class="bg-grey-lighter px-4 py-4 flex items-center">
                <div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path opacity=".45" fill="#263238" d="M9.153 11.603c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962zm-3.204 1.362c-.026-.307-.131 5.218 6.063 5.551 6.066-.25 6.066-5.551 6.066-5.551-6.078 1.416-12.129 0-12.129 0zm11.363 1.108s-.669 1.959-5.051 1.959c-3.505 0-5.388-1.164-5.607-1.959 0 0 5.912 1.055 10.658 0zM11.804 1.011C5.609 1.011.978 6.033.978 12.228s4.826 10.761 11.021 10.761S23.02 18.423 23.02 12.228c.001-6.195-5.021-11.217-11.216-11.217zM12 21.354c-5.273 0-9.381-3.886-9.381-9.159s3.942-9.548 9.215-9.548 9.548 4.275 9.548 9.548c-.001 5.272-4.109 9.159-9.382 9.159zm3.108-9.751c.795 0 1.439-.879 1.439-1.962s-.644-1.962-1.439-1.962-1.439.879-1.439 1.962.644 1.962 1.439 1.962z"></path></svg>
                </div>
                <div class="flex-1 mx-4">
                    <input class="w-full border rounded px-2 py-2" id="message" x-model="$store.inbox.message" type="text"/>
                </div>
                <div @click="$store.inbox.sendMessage()">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                    </svg>
                </div>
            </div>
        </div>

    </main>
    </body>
</html>
