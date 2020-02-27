<?php


namespace App\Http\Controllers\Admin\ScreenShoter;


use App\Entity\Game\ScreenShoter;
use App\Exceptions\Exception;
use App\Http\Controllers\Controller;
use App\NavMenu;
use App\Repository\Game\ScreenShoter\DoctrineScreenShoterRepository;
use App\Repository\Game\ScreenShoter\ScreenShoterRepository;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ScreenShoterController extends Controller
{
    private const countDays = 31;

    public function render()
    {
        NavMenu::$active = 'admin.screenshoter';

        $page = abs((int) request('page', 1));

        /**
         * @var ScreenShoterRepository $repository
         */
        $repository = server_common_connection(DoctrineScreenShoterRepository::class, ScreenShoter::class);

        $date = new \DateTime;
        $date2 = new \DateTime;

        $dateMax = $date->format('Y-m-d');
        $date2->modify('-' . static::countDays . ' day');
        $dateMin = $date2->format('Y-m-d');
        unset($date2);

        $countData = $repository->getCountPerDays($dateMin, $dateMax, request('name'));

        $sorted = [];
        if (isset($countData[$dateMax])) {
            $sorted[] = ['Сегодня', $countData[$dateMax], $dateMax];
            unset($countData[$dateMax]);
        }

        $date->modify('-1 day');

        if (isset($countData[$date->format('Y-m-d')])) {
            $sorted[] = ['Вчера', $countData[$date->format('Y-m-d')], $date->format('Y-m-d')];
            unset($countData[$date->format('Y-m-d')]);
        }

        foreach ($countData as $day => $count)
        {
            $sorted[] = [$day, $count, $day];
        }

        return view('admin.screenshoter.index', [
            'days' => $sorted,
            'username' => request('name')
        ]);
    }

    public function loadDate(Request $request)
    {
        try {
            $this->validate($request, [
                'date' => 'required|date',
                'name' => 'nullable|string'
            ]);

            /**
             * @var ScreenShoterRepository $repository
             */
            $repository = server_common_connection(DoctrineScreenShoterRepository::class, ScreenShoter::class);

            try {
                $date = new \DateTime($request->post('date'));
            } catch (\Exception $exception) {
                throw new Exception($exception->getMessage());
            }

            $page = abs((int) $request->get('page', 1));

            return view('admin.screenshoter.ajax', [
                'screens' => $repository->getForDate($date, $page, $request->post('name'))
            ]);
        } catch (ValidationException $exception) {
            return $exception->validator->errors()->first();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}