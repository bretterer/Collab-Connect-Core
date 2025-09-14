<div>
    <div>
        <img class="block h-10 w-auto dark:hidden"
             src="{{ Vite::asset('resources/images/CollabConnect.png') }}"
             alt="CollabConnect Logo" />
        <img class="hidden h-10 w-auto dark:block"
             src="{{ Vite::asset('resources/images/CollabConnectDark.png') }}"
             alt="CollabConnect Logo" />
        <h2 class="mt-8 text-2xl/9 font-bold tracking-tight text-gray-900 dark:text-white">Sign in to your account</h2>
        @if (Route::has('register'))
        <p class="mt-2 text-sm/6 text-gray-500 dark:text-gray-400">
            Not a member?
            <flux:link :href="route('register')"> Signup Today!</flux:link>
        </p>
        @endif
    </div>

    <div class="mt-10">
        <div>
            @if (session('status'))
                <div class="mb-4 rounded-md bg-green-50 p-4 dark:bg-green-900/20">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800 dark:text-green-200">
                                {{ session('status') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <form
                  class="space-y-6"
                  wire:submit.prevent="login"
                  method="POST">

                <flux:field>
                    <flux:label class="block text-sm/6 font-medium">Email address</flux:label>
                    <flux:input type="email"
                                wire:model="email" />
                    <flux:error name="email" />
                </flux:field>

                <flux:field>
                    <flux:label class="block text-sm/6 font-medium">Password</flux:label>
                    <flux:input type="password"
                                wire:model="password" />
                    <flux:error name="password" />
                </flux:field>


                <div class="flex items-center justify-between">
                    <div class="flex gap-3">
                        <flux:checkbox wire:model="remember"
                                       :label="__('Remember me')" />
                    </div>


                    <flux:link class="text-sm font-semibold"
                               :href="route('password.request')"
                               wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>

                </div>

                <div>
                    <flux:button class="w-full"
                                 type="submit"
                                 variant="primary">{{ __('Log in') }}</flux:button>
                </div>
            </form>
        </div>

        <div class="mt-10">
            @if (app()->environment('local'))
            <div class="relative">
                <div class="absolute inset-0 flex items-center"
                     aria-hidden="true">
                    <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                </div>
                <div class="relative flex justify-center text-sm/6 font-medium">
                    <span class="bg-white px-6 text-gray-900 dark:bg-gray-900 dark:text-gray-100">Or continue with</span>
                </div>
            </div>
            @endif

            <div class="mt-6 grid grid-cols-2 gap-4">

                @if (app()->environment('local'))
                <div
                class="col-span-2 flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700">
               <x-login-link class="text-sm/6 font-semibold"
                             :email="env('INIT_USER_EMAIL')"
                             label="Login as Admin"
                             redirect-url="{{ route('dashboard') }}"/>
           </div>

           <div
                         class="col-span-2 flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700">
                        <x-login-link class="text-sm/6 font-semibold"
                                      :email="env('INIT_BUSINESS_EMAIL')"
                                      label="Login as Business"
                                      redirect-url="{{ route('dashboard') }}"/>
                    </div>

                    <div
                         class="col-span-2 flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700">
                        <x-login-link class="text-sm/6 font-semibold"
                                      :email="env('INIT_INFLUENCER_EMAIL')"
                                      label="Login as Influencer"
                                      redirect-url="{{ route('dashboard') }}"/>
                    </div>
                @endif

                <a class="flex hidden w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700"
                   href="#">
                    <svg class="h-5 w-5"
                         aria-hidden="true"
                         viewBox="0 0 24 24">
                        <path d="M12.0003 4.75C13.7703 4.75 15.3553 5.36002 16.6053 6.54998L20.0303 3.125C17.9502 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.28027 6.60998L5.27028 9.70498C6.21525 6.86002 8.87028 4.75 12.0003 4.75Z"
                              fill="#EA4335" />
                        <path d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L19.945 21.1C22.2 19.01 23.49 15.92 23.49 12.275Z"
                              fill="#4285F4" />
                        <path d="M5.26498 14.2949C5.02498 13.5699 4.88501 12.7999 4.88501 11.9999C4.88501 11.1999 5.01998 10.4299 5.26498 9.7049L1.275 6.60986C0.46 8.22986 0 10.0599 0 11.9999C0 13.9399 0.46 15.7699 1.28 17.3899L5.26498 14.2949Z"
                              fill="#FBBC05" />
                        <path d="M12.0004 24.0001C15.2404 24.0001 17.9654 22.935 19.9454 21.095L16.0804 18.095C15.0054 18.82 13.6204 19.245 12.0004 19.245C8.8704 19.245 6.21537 17.135 5.2654 14.29L1.27539 17.385C3.25539 21.31 7.3104 24.0001 12.0004 24.0001Z"
                              fill="#34A853" />
                    </svg>
                    <span class="text-sm/6 font-semibold">Google</span>
                </a>

                <a class="flex hidden w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:ring-transparent dark:bg-gray-800 dark:text-gray-100 dark:ring-gray-600 dark:hover:bg-gray-700"
                   href="#">
                    <svg class="size-5 fill-[#24292F] dark:fill-gray-100"
                         aria-hidden="true"
                         fill="currentColor"
                         viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M10 0C4.477 0 0 4.484 0 10.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0110 4.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.203 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.942.359.31.678.921.678 1.856 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0020 10.017C20 4.484 15.522 0 10 0z"
                              clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm/6 font-semibold">GitHub</span>
                </a>
            </div>
        </div>
    </div>
</div>